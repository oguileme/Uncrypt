<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name','username', 'email', 'password', 'level', 'xp_progress', 'xp_levelup'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'challenge_user')
            ->withPivot('completed', 'attempts')
            ->withTimestamps();
    }
    
    //metodos de metricas do user
    public function challengesCompleted()
    {
        return $this->challenges()->wherePivot('completed', true)->count();
    }

    public function accuracyRate()
    {
        $totalAttempts = $this->challenges()->sum('challenge_user.attempts');
        $completedChallenges = $this->challengesCompleted();

        if ($totalAttempts === 0) {
            return 0;
        }

        return ($completedChallenges / $totalAttempts) * 100;
    }

    public function avgTimePerChallenge(): float
    {
        return (float) $this->challenges()
            ->wherePivot('completed', true)
            ->avg('challenge_user.time_taken') ?? 0;
    }
}
