<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_promotes_existing_user_to_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->artisan('user:make-admin', ['email' => $user->email])
            ->expectsOutputToContain('administrador')
            ->assertSuccessful();

        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_revokes_admin_role(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->artisan('user:make-admin', ['email' => $user->email, '--revoke' => true])
            ->expectsOutputToContain('deixou de ser administrador')
            ->assertSuccessful();

        $this->assertFalse($user->fresh()->is_admin);
    }

    public function test_fails_when_email_does_not_exist(): void
    {
        $this->artisan('user:make-admin', ['email' => 'ninguem@uncrypt.test'])
            ->assertFailed();
    }
}
