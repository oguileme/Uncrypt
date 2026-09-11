<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'path', 'ip', 'user_agent', 'accessed_at'])]
class AccessLog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'accessed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
