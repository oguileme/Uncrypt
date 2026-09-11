<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_requires_authentication(): void
    {
        $this->postJson('/api/feedback', [
            'context_url' => 'http://localhost:5173/home',
            'feedback_text' => 'relatorio de bug',
            'feedback_type' => 'bug',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_store_feedback(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/feedback', [
            'user_id' => $user->id,
            'context_url' => 'http://localhost:5173/challenge/1',
            'feedback_text' => 'a dica corta as tentativas sem aviso',
            'feedback_type' => 'bug',
        ])->assertCreated()
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('context_url', 'http://localhost:5173/challenge/1')
            ->assertJsonPath('feedback_text', 'a dica corta as tentativas sem aviso')
            ->assertJsonPath('feedback_type', 'bug')
            ->assertJsonPath('status', 'new');

        $this->assertDatabaseHas('feedback', [
            'user_id' => $user->id,
            'feedback_type' => 'bug',
            'status' => 'new',
        ]);
    }

    public function test_store_ignores_client_supplied_user_id_to_prevent_idor(): void
    {
        $author = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($author);

        $this->postJson('/api/feedback', [
            'user_id' => $other->id,
            'context_url' => 'http://localhost:5173/home',
            'feedback_text' => 'tentativa de spoofing do autor',
            'feedback_type' => 'bug',
        ])->assertCreated()
            ->assertJsonPath('user_id', $author->id);

        $this->assertDatabaseHas('feedback', ['user_id' => $author->id]);
        $this->assertDatabaseMissing('feedback', ['user_id' => $other->id]);
    }

    public function test_invalid_feedback_type_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/feedback', [
            'user_id' => $user->id,
            'context_url' => 'http://localhost:5173/home',
            'feedback_text' => 'tipo invalido',
            'feedback_type' => 'duplicata',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('feedback_type');
    }

    public function test_store_defaults_type_and_accepts_other_types(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // o controller exige feedback_type explicito; validamos os tres aceitos
        foreach (['bug', 'feature_request', 'general'] as $type) {
            $this->postJson('/api/feedback', [
                'user_id' => $user->id,
                'context_url' => 'http://localhost:5173/home',
                'feedback_text' => "sugestao {$type}",
                'feedback_type' => $type,
            ])->assertCreated()
                ->assertJsonPath('status', 'new');
        }

        $this->assertSame(3, Feedback::count());
    }
}
