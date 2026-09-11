<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActionLog extends Model
{
    protected $fillable = [
        'admin_user_id',
        'action',
        'target_type',
        'target_id',
        'changes',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
