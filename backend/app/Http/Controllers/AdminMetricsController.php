<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use Illuminate\Support\Facades\Cache;

class AdminMetricsController extends Controller
{
    /**
     * Resumo de metricas do sistema: acessos (total, ultimos 30 dias, ultimos
     * 7 dias) e usuarios ativos (distintos nas mesmas janelas). Apenas o
     * trafego de usuarios comuns conta — contas de admin ficam de fora.
     */
    public function __invoke()
    {
        $metrics = Cache::remember('admin.metrics', 60, function () {
            $base = AccessLog::query()->whereHas('user', fn ($q) => $q->where('is_admin', false));

            $sinceDays = fn ($query, int $days) => (clone $query)->where('accessed_at', '>=', now()->subDays($days));
            $count = fn ($query) => (clone $query)->count();
            $distinctUsers = fn ($query) => (clone $query)->distinct()->count('user_id');

            return [
                'acessos' => [
                    'total' => $count($base),
                    'mes' => $count($sinceDays($base, 30)),
                    'semana' => $count($sinceDays($base, 7)),
                ],
                'usuarios_ativos' => [
                    'total' => $distinctUsers($base),
                    'mes' => $distinctUsers($sinceDays($base, 30)),
                    'semana' => $distinctUsers($sinceDays($base, 7)),
                ],
            ];
        });

        return response()->json($metrics);
    }
}
