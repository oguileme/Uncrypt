<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecentActivityTest extends TestCase
{
    use RefreshDatabase;

    private function activity(User $user, int $index): ChallengeUser
    {
        return ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => Challenge::factory()->create(['title' => "Desafio {$index}"])->id,
            'completed' => $index % 2 === 0,
            'attempts' => $index + 1,
            'created_at' => now()->addSeconds($index),
        ]);
    }

    public function test_recent_activity_defaults_to_last_five_ordered_desc(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 6; $i++) {
            $this->activity($user, $i);
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/recent-activity')
            ->assertOk()
            ->assertJsonCount(5)
            ->assertJsonStructure([[
                'id', 'challenge', 'result', 'time', 'attempts',
            ]]);

        // mais recente primeiro
        $response->assertJsonPath('0.challenge', 'Desafio 5');
        $response->assertJsonPath('0.attempts', 6);

        // resultado mapeado para correct/wrong (index 5 e impar -> nao completado)
        $response->assertJsonPath('0.result', 'wrong');
    }

    public function test_recent_activity_respects_limit_parameter_and_ownership(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 6; $i++) {
            $this->activity($user, $i);
        }

        $other = User::factory()->create();
        $this->activity($other, 99);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/recent-activity?limit=20')
            ->assertOk()
            ->assertJsonCount(6);

        $titles = collect($response->json())->pluck('challenge');
        $this->assertNotContains('Desafio 99', $titles);
        $this->assertContains('Desafio 0', $titles);
    }

    public function test_recent_activity_ignores_records_without_attempts(): void
    {
        $user = User::factory()->create();
        $this->activity($user, 0);

        $challenge = Challenge::factory()->create(['title' => 'Nunca Tentado']);
        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 0,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/user/recent-activity')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.challenge', 'Desafio 0');
    }
}