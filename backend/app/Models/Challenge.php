<?php

namespace App\Models;

use App\Helpers\CipherHelper;
use Illuminate\Database\Eloquent\Model;


class Challenge extends Model
{
    //
    protected $table = 'challenge';

    protected $fillable = ['title', 'description', 'type_encryption_id', 'phrase', 'key', 'xp', 'is_active'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->withPivot('completed', 'attempts')
            ->withTimestamps();
    }


    public function typeEncryption()
    {
        return $this->belongsTo(TypeEncrypton::class);
    }

    // anexa o texto cifrado (gerado pelo CipherHelper) ao desafio, escondendo a resposta original
    public function withCiphertext(): self
    {
        $this->load('typeEncryption');
        $this->ciphertext = CipherHelper::encryptByTypeName(
            $this->typeEncryption->name,
            $this->phrase,
            $this->key
        );

        return $this;
    }
}
