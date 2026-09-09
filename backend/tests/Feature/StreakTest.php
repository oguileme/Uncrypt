<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StreakTest extends TestCase
{
    use RefreshDatabase;

    private function completeChallenge(User $user, string $phrase): void
    {
        $challenge = Challenge::factory()->create(['phrase' => $phrase]);
        $record = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
        ]);

        $this->postJson("/api/challenge-users/{$record->id}/attempt", [
            'attempt' => $phrase,
        ])->assertOk()
            ->assertJsonPath('completed', true);
    }

    public function test_first_completion_starts_streak_at_one(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->completeChallenge($user, 'primeira resposta');

        $user->refresh();
        $this->assertSame(1, $user->current_streak);
        $this->assertSame(now()->toDateString(), $user->streak_last_day->toDateString());
    }

    public function test_completion_on_consecutive_day_increments_streak(): void
    {
        $user = User::factory()->create([
            'current_streak' => 5,
            'streak_last_day' => now()->subDay(),
        ]);
        Sanctum::actingAs($user);

        $this->completeChallenge($user, 'dia consecutivo');

        $user->refresh();
        $this->assertSame(6, $user->current_streak);
    }

    public function test_multiple_completions_same_day_do_not_inflate_streak(): void
    {
        $user = User::factory()->create([
            'current_streak' => 3,
            'streak_last_day' => now(),
        ]);
        Sanctum::actingAs($user);

        $this->completeChallenge($user, 'primeiro do dia');
        $user->refresh();
        $this->assertSame(3, $user->current_streak);

        $this->completeChallenge($user, 'segundo do dia');
        $user->refresh();
        $this->assertSame(3, $user->current_streak);
    }

    public function test_completion_after_a_gap_resets_streak_to_one(): void
    {
        $user = User::factory()->create([
            'current_streak' => 9,
            'streak_last_day' => now()->subDays(2),
        ]);
        Sanctum::actingAs($user);

        $this->completeChallenge($user, 'depois da quebra');

        $user->refresh();
        $this->assertSame(1, $user->current_streak);
        $this->assertSame(now()->toDateString(), $user->streak_last_day->toDateString());
    }

    public function test_metrics_expose_streak_and_cache_is_invalidated_on_completion(): void
    {
        $user = User::factory()->create([
            'current_streak' => 2,
            'streak_last_day' => now()->subDay(),
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/user/metrics')
            ->assertOk()
            ->assertJsonPath('current_streak', 2);
        $this->assertTrue(Cache::has("user.metrics.{$user->id}"));

        $this->completeChallenge($user, 'streak atualizado');

        $this->assertFalse(Cache::has("user.metrics.{$user->id}"));

        $this->getJson('/api/user/metrics')
            ->assertOk()
            ->assertJsonPath('current_streak', 3);
    }

    public function test_first_day_completion_has_no_streak_bonus(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $challenge = Challenge::factory()->create(['phrase' => 'primeiro dia sem bonus', 'xp' => 73]);
        $record = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
        ]);

        $this->postJson("/api/challenge-users/{$record->id}/attempt", [
            'attempt' => 'primeiro dia sem bonus',
        ])->assertOk()
            ->assertJsonPath('streak_days', 1)
            ->assertJsonPath('streak_bonus', 0)
            ->assertJsonPath('xp_gained', 73);
    }

    public function test_streak_bonus_scales_from_second_consecutive_day(): void
    {
        $user = User::factory()->create([
            'current_streak' => 6,
            'streak_last_day' => now()->subDay(),
        ]);
        Sanctum::actingAs($user);

        $challenge = Challenge::factory()->create(['phrase' => 'bonus de streak', 'xp' => 100]);
        $record = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
        ]);

        // 7 dias de streak -> multiplicador 1.30 -> +30 XP sobre os 100 base
        $this->postJson("/api/challenge-users/{$record->id}/attempt", [
            'attempt' => 'bonus de streak',
        ])->assertOk()
            ->assertJsonPath('streak_days', 7)
            ->assertJsonPath('xp_gained', 130)
            ->assertJsonPath('streak_bonus', 30);
    }

    public function test_streak_bonus_caps_at_100_percent(): void
    {
        $user = User::factory()->create([
            'current_streak' => 25,
            'streak_last_day' => now()->subDay(),
        ]);
        Sanctum::actingAs($user);

        $challenge = Challenge::factory()->create(['phrase' => 'bonus no teto', 'xp' => 100]);
        $record = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
        ]);

        // 26 dias -> multiplier cap 2.0 -> dobra o XP
        $this->postJson("/api/challenge-users/{$record->id}/attempt", [
            'attempt' => 'bonus no teto',
        ])->assertOk()
            ->assertJsonPath('streak_days', 26)
            ->assertJsonPath('xp_gained', 200)
            ->assertJsonPath('streak_bonus', 100);
    }

    public function test_streak_bonus_applies_after_hint_halving(): void
    {
        $user = User::factory()->create([
            'current_streak' => 10,
            'streak_last_day' => now()->subDay(),
        ]);
        Sanctum::actingAs($user);

        $challenge = Challenge::factory()->create(['phrase' => 'bonus com dica', 'xp' => 100]);
        $record = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
            'hint_used' => true,
        ]);

        // base 50 (dica) x 1.50 (11 dias) = 75; bonus = 25
        $this->postJson("/api/challenge-users/{$record->id}/attempt", [
            'attempt' => 'bonus com dica',
        ])->assertOk()
            ->assertJsonPath('streak_days', 11)
            ->assertJsonPath('xp_gained', 75)
            ->assertJsonPath('streak_bonus', 25);
    }
}
