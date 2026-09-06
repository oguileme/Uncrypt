<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_write_type_encryption(): void
    {
        $this->postJson('/api/type-encryption', [
            'name' => 'Nova cifra',
            'description' => 'Descricao',
            'difficulty' => 'easy',
        ])->assertStatus(401);
    }

    public function test_non_admin_user_gets_forbidden(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/type-encryption', [
            'name' => 'Nova cifra',
            'description' => 'Descricao',
            'difficulty' => 'easy',
        ])->assertStatus(403)
            ->assertJsonPath('message', 'Forbidden');
    }

    public function test_admin_can_create_type_encryption(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $this->postJson('/api/type-encryption', [
            'name' => 'Nova cifra',
            'description' => 'Descricao',
            'difficulty' => 'hard',
        ])->assertStatus(201)
            ->assertJsonPath('name', 'Nova cifra');

        $this->assertDatabaseHas('type_encryption', ['name' => 'Nova cifra']);
    }

    public function test_admin_can_update_and_delete_achievement(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $achievement = \App\Models\Achievement::factory()->create();

        $this->putJson("/api/achievement/{$achievement->id}", [
            'name' => 'Conquista renomeada',
        ])->assertOk()
            ->assertJsonPath('name', 'Conquista renomeada');

        $this->assertDatabaseHas('achievements', [
            'id' => $achievement->id,
            'name' => 'Conquista renomeada',
        ]);

        $this->deleteJson("/api/achievement/{$achievement->id}")->assertStatus(204);
        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }
}