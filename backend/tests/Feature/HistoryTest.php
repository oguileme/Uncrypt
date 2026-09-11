<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_requires_authentication(): void
    {
        $this->getJson('/api/user/history')->assertUnauthorized();
    }

    public function test_history_returns_own_records_with_challenge_data(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $challenge = Challenge::factory()->create(['title' => 'César fácil', 'xp' => 40]);

        $mine = ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => true,
            'attempts' => 2,
            'hint_used' => true,
            'time_taken' => 90,
        ]);

        // registro de outro usuario nao pode vazar no historico (IDOR)
        ChallengeUser::create([
            'user_id' => $other->id,
            'challenge_id' => $challenge->id,
            'completed' => true,
            'attempts' => 1,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/user/history')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.challenge', 'César fácil')
            ->assertJsonPath('data.0.type', $challenge->typeEncryption->name)
            ->assertJsonPath('data.0.xp', 40)
            ->assertJsonPath('data.0.completed', true)
            ->assertJsonPath('data.0.hint_used', true)
            ->assertJsonPath('data.0.time_taken', 90)
            ->assertJsonPath('data.0.concluded_at', $mine->fresh()->updated_at->toIso8601String());
    }

    public function test_history_exposes_in_progress_records_without_concluded_at(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create();

        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => false,
            'attempts' => 3,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/user/history')
            ->assertOk()
            ->assertJsonPath('data.0.completed', false)
            ->assertJsonPath('data.0.concluded_at', null);
    }

    public function test_history_is_paginated_with_default_order(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 6) as $i) {
            $challenge = Challenge::factory()->create();
            $record = ChallengeUser::create([
                'user_id' => $user->id,
                'challenge_id' => $challenge->id,
                'completed' => $i % 2 === 0,
                'attempts' => $i,
            ]);
            // atualizado_at distinto para validar a ordem (mais recente primeiro)
            $record->updated_at = now()->addMinutes($i);
            $record->timestamps = false;
            $record->save();
        }

        Sanctum::actingAs($user);

        $this->getJson('/api/user/history?per_page=5')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('total', 6)
            ->assertJsonPath('last_page', 2)
            ->assertJsonPath('data.0.attempts', 6);
    }
}
