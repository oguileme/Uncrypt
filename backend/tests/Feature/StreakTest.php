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
}