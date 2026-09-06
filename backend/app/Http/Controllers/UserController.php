<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\ChallengeUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(User::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create($data);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255',
        ]);
        $user->update($data);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function getUserMetrics()
    {
        if (!auth()->check()) {
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
            ];
        });

        return response()->json($metrics);
    }

    /**
     * Retorna as ultimas atividades do usuario logado em challenge_user.
     */
    public function getRecentActivity(Request $request)
    {
        if (!auth()->check()) {
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
                'id'        => $cu->id,
                'challenge' => $cu->challenge?->title ?? 'Desafio excluído',
                'result'    => $cu->completed ? 'correct' : 'wrong',
                'time'      => $cu->created_at->locale('pt_BR')->diffForHumans(),
                'attempts'  => $cu->attempts,
            ]);

        return response()->json($activities);
    }
}