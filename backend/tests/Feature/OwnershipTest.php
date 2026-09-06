<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\AchievementUser;
use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnershipTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function challengeUser(User $user, bool $completed = false): ChallengeUser
    {
        $challenge = Challenge::factory()->create();

        return ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'completed' => $completed,
            'attempts' => 0,
        ]);
    }

    public function test_challenge_users_index_returns_only_own_records(): void
    {
        $owner = $this->user();
        $attacker = $this->user();

        $this->challengeUser($owner);
        $this->challengeUser($owner);
        $attackerRecord = $this->challengeUser($attacker);

        Sanctum::actingAs($attacker);

        $response = $this->getJson('/api/challenge-users')
            ->assertOk()
            ->assertJsonCount(1);

        $response->assertJsonPath('0.id', $attackerRecord->id);
    }

    public function test_update_of_foreign_challenge_user_is_forbidden(): void
    {
        $owner = $this->user();
        $attacker = $this->user();

        $record = $this->challengeUser($owner);

        Sanctum::actingAs($attacker);

        $this->putJson("/api/challenge-users/{$record->id}", [
            'hint_used' => true,
        ])->assertStatus(403);

        $this->assertSame(0, $record->fresh()->hint_used);
    }

    public function test_show_of_foreign_challenge_user_is_forbidden(): void
    {
        $owner = $this->user();
        $attacker = $this->user();

        $record = $this->challengeUser($owner);

        Sanctum::actingAs($attacker);

        $this->getJson("/api/challenge-users/{$record->id}")->assertStatus(403);
    }

    public function test_achievement_progress_store_always_uses_authenticated_user(): void
    {
        $achievement = Achievement::factory()->create();
        $user = $this->user();

        Sanctum::actingAs($user);

        $this->postJson('/api/achievement-progress', [
            'achievement_id' => $achievement->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('achievements_users', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
        ]);
    }

    public function test_achievement_progress_show_and_update_are_owner_only(): void
    {
        $owner = $this->user();
        $attacker = $this->user();

        $achievement = Achievement::factory()->create();
        $record = AchievementUser::create([
            'user_id' => $owner->id,
            'achievement_id' => $achievement->id,
        ]);

        Sanctum::actingAs($attacker);

        $this->getJson("/api/achievement-progress/{$record->id}")->assertStatus(403);

        $this->putJson("/api/achievement-progress/{$record->id}", [
            'progress' => 10,
            'is_completed' => true,
        ])->assertStatus(403);
    }
}