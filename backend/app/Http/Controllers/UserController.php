<?php

namespace App\Http\Controllers;

use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Retorna as metricas agregadas do usuario logado.
     */
    public function getUserMetrics()
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();

        // agregacao em uma unica query + cache curto (invalida no awardXp)
        $metrics = Cache::remember("user.metrics.{$userId}", 60, function () use ($userId) {
            $stats = ChallengeUser::query()
                ->where('user_id', $userId)
                ->selectRaw('COUNT(*) FILTER (WHERE completed = true) as completed')
                ->selectRaw('COALESCE(SUM(attempts), 0) as attempts')
                ->selectRaw('AVG(time_taken) FILTER (WHERE completed = true) as avg_time_taken')
                ->first();

            $attempts = (int) $stats->attempts;

            return [
                'challenges_completed' => (int) $stats->completed,
                'accuracy_rate' => round($attempts > 0 ? ($stats->completed / $attempts) * 100 : 0, 1),
                'avg_time_per_challenge' => round((float) $stats->avg_time_taken),
                'current_streak' => (int) auth()->user()->current_streak,
            ];
        });

        return response()->json($metrics);
    }

    /**
     * Retorna as ultimas atividades do usuario logado em challenge_user.
     */
    public function getRecentActivity(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // limite padrao 5, aceita ate 20 para evitar payloads pesados
        $limit = max(1, min((int) $request->integer('limit', 5), 20));

        $activities = ChallengeUser::where('user_id', auth()->id())
            ->where('attempts', '>', 0)
            ->with('challenge:id,title')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($cu) => [
                'id' => $cu->id,
                'challenge' => $cu->challenge?->title ?? 'Desafio excluído',
                'result' => $cu->completed ? 'correct' : 'wrong',
                'time' => $cu->created_at->locale('pt_BR')->diffForHumans(),
                'attempts' => $cu->attempts,
            ]);

        return response()->json($activities);
    }

    /**
     * Retorna o historico de desafios do usuario logado (paginaod).
     */
    public function getHistory(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $perPage = max(5, min((int) $request->integer('per_page', 15), 50));

        $history = ChallengeUser::where('user_id', auth()->id())
            ->with(['challenge:id,title,xp,type_encryption_id', 'challenge.typeEncryption:id,name'])
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->through(fn ($cu) => [
                'id' => $cu->id,
                'challenge' => $cu->challenge?->title ?? 'Desafio excluído',
                'type' => $cu->challenge?->typeEncryption?->name ?? null,
                'xp' => $cu->challenge?->xp,
                'completed' => (bool) $cu->completed,
                'attempts' => $cu->attempts,
                'hint_used' => (bool) $cu->hint_used,
                'time_taken' => $cu->time_taken,
                'concluded_at' => $cu->completed ? $cu->updated_at->toIso8601String() : null,
            ]);

        return response()->json($history);
    }

    /**
     * Retorna o ranking dos usuarios por nivel e progresso de XP.
     */
    public function getRanking(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // padrao 10, aceita ate 50; a chave inclui o limite para nao vazar ordenacao entre pedidos
        $limit = max(10, min((int) $request->integer('limit', 10), 50));

        $ranking = Cache::remember("ranking.top.{$limit}", 60, function () use ($limit) {
            return User::query()
                ->orderByDesc('level')
                ->orderByDesc('xp_progress')
                ->orderBy('name')
                ->limit($limit)
                ->get(['id', 'name', 'username', 'level', 'xp_progress', 'current_streak'])
                ->toArray();
        });

        return response()->json($ranking);
    }
}
