<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeUser extends Model
{
    //
    protected $table = 'challenge_user';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'is_complete',
        'attempts',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }
}
