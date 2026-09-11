<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private function feedback(array $overrides = []): Feedback
    {
        return Feedback::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'context_url' => 'http://localhost:5173/home',
            'feedback_text' => 'bug de teste',
            'feedback_type' => 'bug',
            'status' => 'new',
        ], $overrides));
    }

    public function test_guest_cannot_list_feedback(): void
    {
        $this->getJson('/api/feedback')->assertStatus(401);
    }

    public function test_non_admin_is_forbidden_from_all_management_routes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $feedback = $this->feedback();

        $this->getJson('/api/feedback')->assertStatus(403);
        $this->getJson("/api/feedback/{$feedback->id}")->assertStatus(403);
        $this->patchJson("/api/feedback/{$feedback->id}", ['status' => 'resolved'])->assertStatus(403);
        $this->deleteJson("/api/feedback/{$feedback->id}")->assertStatus(403);
    }

    public function test_admin_can_list_paginated_and_filtered_feedback(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $this->feedback(['feedback_text' => 'bug 1', 'feedback_type' => 'bug']);
        $this->feedback(['feedback_text' => 'bug 2', 'feedback_type' => 'bug']);
        $this->feedback(['feedback_text' => 'sugestao', 'feedback_type' => 'general']);

        $this->getJson('/api/feedback?per_page=2&status=new&feedback_type=bug')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('total', 2)
            ->assertJsonStructure(['data', 'current_page', 'last_page', 'total']);

        // paginacao respeita o teto de 50 por pagina
        $this->getJson('/api/feedback?per_page=999')
            ->assertOk()
            ->assertJsonPath('per_page', 50);
    }

    public function test_admin_can_change_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $feedback = $this->feedback();

        $this->patchJson("/api/feedback/{$feedback->id}", ['status' => 'resolved'])
            ->assertOk()
            ->assertJsonPath('status', 'resolved');

        $this->assertDatabaseHas('feedback', ['id' => $feedback->id, 'status' => 'resolved']);
    }

    public function test_admin_cannot_rewrite_user_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $feedback = $this->feedback(['feedback_text' => 'texto original']);

        $this->patchJson("/api/feedback/{$feedback->id}", [
            'status' => 'in_progress',
            'feedback_text' => 'texto alterado pelo admin',
        ])->assertOk();

        $this->assertDatabaseHas('feedback', [
            'id' => $feedback->id,
            'feedback_text' => 'texto original',
        ]);
    }

    public function test_admin_can_delete_feedback(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        $feedback = $this->feedback();

        $this->deleteJson("/api/feedback/{$feedback->id}")->assertStatus(204);
        $this->assertDatabaseMissing('feedback', ['id' => $feedback->id]);
    }
}
