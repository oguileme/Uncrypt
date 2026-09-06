<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cobre o handshake do Sanctum SPA: endpooint /sanctum/csrf-cookie e o fato de
 * requisicoes stateful (Origin do frontend) rodarem a pipeline de sessao.
 * (A rejeicao 419 do CSRF nao e assertavel em PHPUnit: o middleware pula a
 * validacao quando runningUnitTests().)
 */
class StatefulAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_cookie_endpoint_sets_xsrf_and_session_cookies(): void
    {
        $response = $this->withHeader('Origin', 'http://localhost')
            ->get('/sanctum/csrf-cookie');

        $response->assertNoContent(204);

        $cookieNames = collect($response->baseResponse->headers->getCookies())
            ->map(fn ($cookie) => $cookie->getName())
            ->all();

        $this->assertContains('XSRF-TOKEN', $cookieNames);
        $this->assertContains('laravel-session', $cookieNames);
    }

    public function test_stateful_register_starts_session_and_sets_cookie(): void
    {
        $response = $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/register', [
                'name' => 'Stateful',
                'username' => 'stateful',
                'email' => 'stateful@uncrypt.test',
                'password' => 'senha-segura-123',
                'password_confirmation' => 'senha-segura-123',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'stateful@uncrypt.test']);

        $cookieNames = collect($response->baseResponse->headers->getCookies())
            ->map(fn ($cookie) => $cookie->getName())
            ->all();

        // a pipeline SPA (StartSession) rodou: espera o cookie de sessao
        $this->assertContains('laravel-session', $cookieNames);
    }
}