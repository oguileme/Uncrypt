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
        // (HttpCache middleware) definem o proprio Cache-Control antes deste
        if (! $response->headers->has('Cache-Control')) {
            $response->headers->set('Cache-Control', 'no-store');
        }

        return $response;
    }
}
