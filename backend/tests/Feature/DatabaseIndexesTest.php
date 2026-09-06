<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseIndexesTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_indexes_are_created(): void
    {
        $this->assertIndexes('challenge', ['challenge_type_encryption_id_index']);
        $this->assertIndexes('challenge_user', [
            'challenge_user_challenge_id_index',
            'challenge_user_user_created_index',
            'challenge_user_user_challenge_unique',
        ]);
        $this->assertIndexes('achievements_users', [
            'achievements_users_user_id_index',
            'achievements_users_achievement_id_index',
        ]);
    }

    public function test_duplicate_user_challenge_pair_violates_unique_constraint(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create();

        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
        ]);

        $this->expectException(QueryException::class);

        ChallengeUser::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
        ]);
    }

    private function assertIndexes(string $table, array $expected): void
    {
        $names = collect(Schema::getIndexes($table))->pluck('name');

        foreach ($expected as $index) {
            $this->assertTrue(
                $names->contains($index),
                "Index [{$index}] missing on table [{$table}]."
            );
        }
    }
}