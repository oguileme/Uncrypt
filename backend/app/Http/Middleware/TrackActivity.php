<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackActivity
{
    /**
     * Registra cada requisicao autenticada de usuarios comuns na tabela de
     * acessos (base das metricas do painel admin). O trafego de admins nao e
     * rastreado para nao poluir as metricas.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_admin) {
            AccessLog::create([
                'user_id' => $user->id,
                'path' => substr($request->path(), 0, 255),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'accessed_at' => now(),
            ]);
        }

        return $next($request);
    }
}
