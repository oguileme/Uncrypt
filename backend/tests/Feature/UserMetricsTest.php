<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserMetricsTest extends TestCase
{
    use RefreshDatabase;

    private function metricsUrl(User $user): string
    {
        return '/api/user/metrics';
    }

    private function metricAggregationData(User $user): void
    {
        // 2 completados (3 e 1 tentativas) + 1 incompleto, sem tentativas
        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed' => true,
            'attempts' => 3,
            'time_taken' => 120,
        ]);
        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed' => true,
            'attempts' => 1,
            'time_taken' => 80,
        ]);
        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed' => false,
            'attempts' => 0,
            'time_taken' => null,
        ]);
    }

    public function test_metrics_aggregate_only_own_data_with_expected_values(): void
    {
        $user = User::factory()->create();
        $this->metricAggregationData($user);

        // dados de outro usuario nao devem entrar na conta
        $other = User::factory()->create();
        ChallengeUser::create([
            'user_id' => $other->id,
            'challenge_id' => Challenge::factory()->create()->id,
            'completed' => true,
            'attempts' => 10,
            'time_taken' => 999,
        ]);

        Sanctum::actingAs($user);

        $this->getJson($this->metricsUrl($user))
            ->assertOk()
            ->assertJsonPath('challenges_completed', 2)
            ->assertJsonPath('accuracy_rate', 50)
            ->assertJsonPath('avg_time_per_challenge', 100);
    }

    public function test_metrics_are_cached_after_first_call(): void
    {
        $user = User::factory()->create();
        $this->metricAggregationData($user);

        Sanctum::actingAs($user);

        $this->getJson($this->metricsUrl($user))->assertOk();
        $this->assertTrue(Cache::has("user.metrics.{$user->id}"));
    }

    public function test_metrics_cache_is_invalidated_after_completing_a_challenge(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create(['phrase' => 'resposta de teste']);
        $record = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
        ]);

        Sanctum::actingAs($user);

        $this->getJson($this->metricsUrl($user))
            ->assertOk()
            ->assertJsonPath('challenges_completed', 0);
        $this->assertTrue(Cache::has("user.metrics.{$user->id}"));

        $this->postJson("/api/challenge-users/{$record->id}/attempt", [
            'attempt' => 'resposta de teste',
        ])->assertOk()
            ->assertJsonPath('completed', true)
            ->assertJsonPath('xp_gained', $challenge->xp);

        // cache invalidado no awardXp: a proxima leitura reflete a conclusao
        $this->assertFalse(Cache::has("user.metrics.{$user->id}"));

        $this->getJson($this->metricsUrl($user))
            ->assertOk()
            ->assertJsonPath('challenges_completed', 1)
            ->assertJsonPath('accuracy_rate', 100);
    }
}