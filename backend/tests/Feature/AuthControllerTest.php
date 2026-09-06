<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Stubs\NoOpCsrf;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * As rotas de register/login/logout vivem no fluxo stateful (sessao + cookie).
     * A validacao do token XSRF e coberta em StatefulAuthTest; aqui apenas
     * desligamos a checagem para conseguir exercitar o comportamento do guard.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('sanctum.middleware.validate_csrf_token', NoOpCsrf::class);
    }

    public function test_register_creates_user_auto_logs_in_and_returns_no_token(): void
    {
        $password = 'senha-segura-123';

        $response = $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/register', [
                'name' => 'Novo User',
                'username' => 'novouser_teste',
                'email' => 'novo@uncrypt.test',
                'password' => $password,
                'password_confirmation' => $password,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('email', 'novo@uncrypt.test')
            ->assertJsonPath('name', 'Novo User')
            ->assertJsonMissingPath('token');

        $this->assertDatabaseHas('users', [
            'email' => 'novo@uncrypt.test',
            'username' => 'novouser_teste',
        ]);
        $this->assertAuthenticated('web');
    }

    public function test_register_requires_confirmed_password(): void
    {
        $response = $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/register', [
                'name' => 'Novo User',
                'username' => 'novouser_teste2',
                'email' => 'novo2@uncrypt.test',
                'password' => 'senha-segura-123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'novo2@uncrypt.test']);
        $this->assertGuest('web');
    }

    public function test_login_succeeds_and_returns_user_without_token(): void
    {
        User::factory()->create([
            'email' => 'login@uncrypt.test',
            'password' => 'password',
        ]);

        $response = $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/login', [
                'email' => 'login@uncrypt.test',
                'password' => 'password',
            ]);

        $response->assertOk()
            ->assertJsonPath('user.email', 'login@uncrypt.test')
            ->assertJsonMissingPath('user.token');

        $this->assertAuthenticated('web');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@uncrypt.test',
            'password' => 'password',
        ]);

        $response = $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/login', [
                'email' => 'login@uncrypt.test',
                'password' => 'senha-errada-999',
            ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Invalid credentials');

        $this->assertGuest('web');
    }

    public function test_logout_clears_the_web_session(): void
    {
        $user = User::factory()->create();

        // session do usuario definida (equivalente ao browser autenticado por cookie)
        $this->actingAs($user);

        $response = $this->withHeader('Origin', 'http://localhost')
            ->postJson('/api/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logout successful');

        $this->assertGuest('web');
    }
}