<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpCache
{
    // habilita cache HTTP no browser (maxAge em segundos, scope public/private)
    // com validacao por ETag: respostas 304 sem corpo quando o cache esta fresco
    public function handle(Request $request, Closure $next, string $maxAge = '1800', string $scope = 'public'): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', "{$scope}, max-age={$maxAge}, must-revalidate");
        $response->headers->set('ETag', '"' . md5($response->getContent()) . '"');

        if ($request->isNotModified($response)) {
            return $response->setNotModified();
        }

        return $response;
    }
}