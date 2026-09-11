<?php

namespace Tests\Feature;

use App\Models\AccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TrackActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_request_is_logged(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        Sanctum::actingAs($user);

        $this->getJson('/api/ranking')->assertOk();

        $this->assertSame(1, AccessLog::count());
        $log = AccessLog::firstOrFail();
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('api/ranking', $log->path);
        $this->assertNotNull($log->ip);
        $this->assertNotNull($log->user_agent);
        $this->assertNotNull($log->accessed_at);
    }

    public function test_unauthenticated_request_is_not_logged(): void
    {
        $this->getJson('/api/ranking')->assertUnauthorized();

        $this->assertSame(0, AccessLog::count());
    }

    public function test_admin_request_is_not_logged(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $this->getJson('/api/ranking')->assertOk();

        $this->assertSame(0, AccessLog::count());
    }
}
