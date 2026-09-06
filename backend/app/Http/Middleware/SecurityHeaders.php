<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        // default de privacidade: sem cache; rotas publicas que liberam cache
        // (HttpCache middleware) definem o proprio Cache-Control antes deste.
        // O Symfony prepara "no-cache, private" implicito em todas as respostas,
        // entao precisamos substituir esse default, nao so o header ausente.
        $cacheControl = $response->headers->get('Cache-Control');
        if (! $cacheControl || $cacheControl === 'no-cache, private') {
            $response->headers->set('Cache-Control', 'no-store');
        }

        return $response;
    }
}
