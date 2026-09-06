<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_api_responses(): void
    {
        $this->getJson('/api/type-encryption')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    public function test_cache_control_defaults_to_no_store_without_http_cache(): void
    {
        // rota sem cache HTTP explicito mantem o padrao privado definido pelo SecurityHeaders.
        // O header pode vir como "no-store, private" porque o Symfony recomputa o
        // Cache-Control sempre que seta ETag/Last-Modified/Expires na resposta.
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');
        $response->assertOk();

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertIsString($cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringNotContainsString('public', $cacheControl);
        $this->assertStringNotContainsString('max-age', $cacheControl);
    }
}