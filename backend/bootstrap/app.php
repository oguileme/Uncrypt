<?php

use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\HttpCache;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrackActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API pura: convidados nao devem ser redirecionados para uma rota 'login' web
        // (que nao existe); o AuthenticationException vira 401 JSON.
        $middleware->redirectGuestsTo(fn () => null);

        // adiciona headers de seguranca HTTP em todas as respostas
        $middleware->append(SecurityHeaders::class);

        // habilita cache HTTP no browser para rotas GET publicas/estaticas
        $middleware->alias([
            'cache.public' => HttpCache::class,
            'admin' => AdminOnly::class,
            'track.activity' => TrackActivity::class,
        ]);

        // reconhece requisicoes do frontend SPA (localhost:5173) via Sanctum,
        // usando sessao + cookie httpOnly em vez de token no localStorage
        $middleware->statefulApi();

        // considera headers X-Forwarded-* apenas de proxies confiaveis (env);
        // vazio = nenhum proxy, $request->ip() reflete o IP real do cliente
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES') ?: [],
            headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT,
        );

        // teto global de requisicoes em todas as rotas da API (+ limites especificos)
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
