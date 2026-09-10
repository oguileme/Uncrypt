<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    //
    protected $table = 'feedback';

    protected $attributes = [
        'feedback_type' => 'general',
        'status' => 'new',
    ];

    protected $fillable = [
        'user_id',
        'context_url',
        'feedback_text',
        'feedback_type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
