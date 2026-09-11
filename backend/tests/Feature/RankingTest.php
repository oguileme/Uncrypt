<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ranking_requires_authentication(): void
    {
        $this->getJson('/api/ranking')->assertUnauthorized();
    }

    public function test_ranking_orders_by_level_then_xp_progress(): void
    {
        User::factory()->create(['name' => 'Baixo', 'level' => 1, 'xp_progress' => 10]);
        User::factory()->create(['name' => 'Top', 'level' => 5, 'xp_progress' => 999]);
        User::factory()->create(['name' => 'Meio', 'level' => 3, 'xp_progress' => 300]);
        $logged = User::factory()->create(['name' => 'Eu', 'level' => 2, 'xp_progress' => 50]);
        Sanctum::actingAs($logged);

        $this->getJson('/api/ranking')
            ->assertOk()
            ->assertJsonCount(4)
            ->assertJsonPath('0.name', 'Top')
            ->assertJsonPath('1.name', 'Meio')
            ->assertJsonPath('2.name', 'Eu')
            ->assertJsonPath('3.name', 'Baixo');
    }

    public function test_ranking_tie_breaks_by_xp_progress_desc(): void
    {
        User::factory()->create(['level' => 2, 'xp_progress' => 100]);
        User::factory()->create(['level' => 2, 'xp_progress' => 500]);
        User::factory()->create(['level' => 2, 'xp_progress' => 300]);
        Sanctum::actingAs(User::factory()->create(['level' => 1]));

        $this->getJson('/api/ranking')
            ->assertOk()
            ->assertJsonPath('0.xp_progress', 500)
            ->assertJsonPath('1.xp_progress', 300)
            ->assertJsonPath('2.xp_progress', 100);
    }

    public function test_ranking_defaults_limit_to_ten_and_clamps_at_fifty(): void
    {
        User::factory()->count(15)->create();
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/ranking')->assertOk()->assertJsonCount(10);

        $this->getJson('/api/ranking?limit=5')->assertOk()->assertJsonCount(10);

        $this->getJson('/api/ranking?limit=100')
            ->assertOk()
            ->assertJsonCount(16);
    }

    public function test_ranking_excludes_admin_accounts(): void
    {
        User::factory()->create(['name' => 'AdminTop', 'level' => 99, 'xp_progress' => 9999, 'is_admin' => true]);
        User::factory()->create(['name' => 'ComumTop', 'level' => 5, 'xp_progress' => 500]);
        User::factory()->create(['name' => 'ComumBaixo', 'level' => 1, 'xp_progress' => 10]);
        Sanctum::actingAs(User::factory()->create(['name' => 'Leitor', 'level' => 2]));

        $this->getJson('/api/ranking')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonPath('0.name', 'ComumTop')
            ->assertJsonPath('1.name', 'Leitor')
            ->assertJsonPath('2.name', 'ComumBaixo')
            ->assertJsonMissing([['name' => 'AdminTop']]);

        $this->getJson('/api/ranking?limit=50')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJsonMissing([['name' => 'AdminTop']]);
    }

    public function test_ranking_exposes_streak_but_hides_sensitive_fields(): void
    {
        User::factory()->create(['level' => 8, 'current_streak' => 4]);
        Sanctum::actingAs(User::factory()->create(['level' => 1]));

        $this->getJson('/api/ranking')
            ->assertOk()
            ->assertJsonPath('0.current_streak', 4);

        $this->getJson('/api/ranking')
            ->assertOk()
            ->assertJsonMissing(['password' => '*', 'email' => '*']);
    }
}
