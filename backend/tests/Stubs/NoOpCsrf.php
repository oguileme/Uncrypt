<?php

namespace Tests\Stubs;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Substitui o ValidateCsrfToken nos testes que exercitam o fluxo com sessao
 * (register/login/logout), onde a coreografia do token XSRF pertence ao nivel
 * de integracao com o browser - coberto separadamente em StatefulAuthTest.
 */
class NoOpCsrf
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}