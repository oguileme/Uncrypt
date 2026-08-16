<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Challenge extends Model
{
    //
    protected $table = 'challenge';

    protected $fillable = ['title', 'description', 'type_encryption_id', 'phrase_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->withPivot('is_complete', 'attempts')
            ->withTimestamps();
    }

    public function phrase()
    {
        return $this->belongsTo(Phrase::class);
    }

    public function typeEncryption()
    {
        return $this->belongsTo(TypeEncrypton::class);
    }
}
