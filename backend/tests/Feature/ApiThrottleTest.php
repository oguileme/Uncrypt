<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_api_throttle_returns_429_after_limit(): void
    {
        $request = Request::create('/api/type-encryption', 'GET');
        $limit = RateLimiter::limiter('api')($request);

        // esgota o teto global (120/min) chaveado pelo IP do "cliente"
        // (o middleware hashs limiterName.key -> md5('api'.$limit->key))
        $cacheKey = md5('api'.$limit->key);
        for ($i = 0; $i < $limit->maxAttempts; $i++) {
            RateLimiter::hit($cacheKey, $limit->decaySeconds);
        }

        $this->getJson('/api/type-encryption')->assertStatus(429);
    }

    public function test_below_the_throttle_limit_requests_go_through(): void
    {
        $request = Request::create('/api/type-encryption', 'GET');
        $limit = RateLimiter::limiter('api')($request);

        $this->assertIsObject($limit);
        $this->assertGreaterThan(1, $limit->maxAttempts);

        $this->getJson('/api/type-encryption')
            ->assertOk()
            ->assertJsonStructure([]);
    }
}