<?php

namespace Tests\Feature;

use App\Models\AdminActionLog;
use App\Models\Feedback;
use App\Models\TypeEncrypton;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminAuditTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);

        return $admin;
    }

    public function test_feedback_status_change_is_audited(): void
    {
        $admin = $this->admin();

        $feedback = Feedback::create([
            'user_id' => User::factory()->create()->id,
            'context_url' => 'http://localhost/x',
            'feedback_text' => 'reportar bug',
            'feedback_type' => 'bug',
        ]);

        $this->patchJson("/api/feedback/{$feedback->id}", ['status' => 'in_progress'])->assertOk();

        $this->assertDatabaseHas('admin_action_logs', [
            'admin_user_id' => $admin->id,
            'action' => 'feedback.status_changed',
            'target_type' => 'feedback',
            'target_id' => $feedback->id,
        ]);
    }

    public function test_feedback_deletion_is_audited(): void
    {
        $admin = $this->admin();

        $feedback = Feedback::create([
            'user_id' => User::factory()->create()->id,
            'context_url' => 'http://localhost/x',
            'feedback_text' => 'spam',
            'feedback_type' => 'general',
        ]);

        $this->deleteJson("/api/feedback/{$feedback->id}")->assertStatus(204);

        $this->assertDatabaseHas('admin_action_logs', [
            'admin_user_id' => $admin->id,
            'action' => 'feedback.deleted',
            'target_type' => 'feedback',
            'target_id' => $feedback->id,
        ]);
    }

    public function test_challenge_audit_omits_sensitive_fields(): void
    {
        $this->admin();

        $type = TypeEncrypton::factory()->create();

        $this->postJson('/api/challenges', [
            'title' => 'Desafio auditado',
            'description' => 'descricao',
            'type_encryption_id' => $type->id,
            'phrase' => 'frase secreta ultra-sensivel',
            'key' => 'chave-secreta',
            'xp' => 30,
            'hint' => 'dica',
        ])->assertStatus(201);

        $log = AdminActionLog::where('action', 'challenge.created')->firstOrFail();

        $changes = $log->changes ?? [];
        $this->assertArrayHasKey('title', $changes);
        $this->assertArrayNotHasKey('phrase', $changes);
        $this->assertArrayNotHasKey('key', $changes);
    }

    public function test_achievement_creation_is_audited(): void
    {
        $this->admin();

        $this->postJson('/api/achievement', [
            'name' => 'Primeira conquista',
            'description' => 'descricao',
            'xp_reward' => 50,
            'required_count' => 1,
            'icon' => 'star',
            'color' => 'green',
        ])->assertStatus(201);

        $this->assertDatabaseHas('admin_action_logs', [
            'action' => 'achievement.created',
            'target_type' => 'achievement',
        ]);
    }
}
