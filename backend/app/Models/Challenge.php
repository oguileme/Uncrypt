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

    // garante que o ciphertext seja sempre gerado e persistido ao criar/alterar
    // phrase, key ou o tipo de cifra (materializado no banco para evitar CPU por request)
    protected static function booted(): void
    {
        static::creating(function (Challenge $challenge) {
            $challenge->computeCiphertext();
        });

        static::updating(function (Challenge $challenge) {
            if ($challenge->isDirty(['phrase', 'key', 'type_encryption_id'])) {
                $challenge->computeCiphertext();
            }
        });
    }

    // gera o texto cifrado a partir dados persistidos e anexa ao model
    public function computeCiphertext(): void
    {
        $this->load('typeEncryption');
        $this->ciphertext = CipherHelper::encryptByTypeName(
            $this->typeEncryption->name,
            $this->phrase,
            $this->key
        );
    }

    // anexa o texto cifrado ao desafio, escondendo a resposta original;
    // gera e persiste na primeira leitura (backfill) e passa a ler da coluna
    public function withCiphertext(): self
    {
        $this->load('typeEncryption');

        if (blank($this->ciphertext)) {
            $this->computeCiphertext();
            $this->saveQuietly();
        }

        return $this;
    }
}
