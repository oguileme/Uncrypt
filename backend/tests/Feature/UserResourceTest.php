<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_exposes_whitelisted_fields_including_is_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('is_admin', true)
            ->assertJsonMissingPath('password')
            ->assertJsonMissingPath('remember_token');
    }

    public function test_non_admin_user_gets_false_boolean(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('is_admin', false);
    }
}
