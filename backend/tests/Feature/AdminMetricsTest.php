<?php

namespace Tests\Feature;

use App\Models\AccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminMetricsTest extends TestCase
{
    use RefreshDatabase;

    private function access(User $user, int $daysAgo = 0): void
    {
        AccessLog::create([
            'user_id' => $user->id,
            'path' => 'api/ranking',
            'ip' => '127.0.0.1',
            'user_agent' => 'pest',
            'accessed_at' => now()->subDays($daysAgo),
        ]);
    }

    public function test_guest_cannot_access_metrics(): void
    {
        $this->getJson('/api/admin/metrics')->assertUnauthorized();
    }

    public function test_non_admin_cannot_access_metrics(): void
    {
        Sanctum::actingAs(User::factory()->create(['is_admin' => false]));

        $this->getJson('/api/admin/metrics')->assertForbidden();
    }

    public function test_admin_sees_access_and_active_user_counts(): void
    {
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $u3 = User::factory()->create();
        $u4 = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->access($u1, 0);
        $this->access($u1, 0);
        $this->access($u2, 0);
        $this->access($u3, 10);
        $this->access($u4, 40);
        $this->access($admin, 0);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/metrics')
            ->assertOk()
            ->assertJsonPath('acessos.total', 5)
            ->assertJsonPath('acessos.mes', 4)
            ->assertJsonPath('acessos.semana', 3)
            ->assertJsonPath('usuarios_ativos.total', 4)
            ->assertJsonPath('usuarios_ativos.mes', 3)
            ->assertJsonPath('usuarios_ativos.semana', 2);
    }
}
