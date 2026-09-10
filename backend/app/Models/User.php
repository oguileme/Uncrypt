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

#[Fillable(['name','username', 'email', 'password', 'level', 'xp_progress', 'xp_levelup', 'current_streak', 'streak_last_day'])]
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
            'streak_last_day' => 'date',
        ];
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'challenge_user')
            ->withPivot('completed', 'attempts')
            ->withTimestamps();
    }

    /**
     * Registra um dia de atividade na sequencia de dias consecutivos.
     * Idempotente: repetir conclusoes no mesmo dia nao incrementa de novo.
     */
    public function touchStreak(): void
    {
        $today = now()->toDateString();

        if ($this->streak_last_day?->toDateString() === $today) {
            return;
        }

        $this->current_streak = $this->streak_last_day?->toDateString() === now()->subDay()->toDateString()
            ? $this->current_streak + 1
            : 1;

        $this->streak_last_day = today();
        $this->save();
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
