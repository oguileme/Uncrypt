<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'user:make-admin {email} {--revoke}';

    protected $description = 'Eleva um usuario a administrador (ou rebaixa com --revoke). Apenas via CLI, nunca por HTTP.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Nenhum usuario com o email: {$email}");

            return self::FAILURE;
        }

        $user->is_admin = ! $this->option('revoke');
        $user->save();

        $user->fresh()->is_admin
            ? $this->info("OK: {$email} agora e administrador.")
            : $this->info("OK: {$email} deixou de ser administrador.");

        return self::SUCCESS;
    }
}
