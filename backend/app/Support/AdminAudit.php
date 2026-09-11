<?php

namespace App\Support;

use App\Models\AdminActionLog;
use Illuminate\Http\Request;

class AdminAudit
{
    /**
     * Registra uma acao administrativa sensivel (quem, o que, quando, de onde).
     *
     * @param  array{action: string, target_type?: ?string, target_id?: ?int, changes?: ?array, admin_user_id?: ?int}  $context
     */
    public static function record(array $context, ?Request $request = null): AdminActionLog
    {
        $request ??= request();

        return AdminActionLog::create([
            'admin_user_id' => $context['admin_user_id'] ?? auth()->id(),
            'action' => $context['action'],
            'target_type' => $context['target_type'] ?? null,
            'target_id' => $context['target_id'] ?? null,
            'changes' => $context['changes'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
    }
}
