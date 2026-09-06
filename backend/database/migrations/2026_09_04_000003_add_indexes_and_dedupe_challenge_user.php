<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Elimina duplicatas em challenge_user e cria indices
     * para joins/filtros frequentes (Postgres nao indexa FKs sozinho).
     */
    public function up(): void
    {
        // remove registros duplicados de (user_id, challenge_id), mantendo o menor id
        // (sintaxe DELETE...USING e exclusiva do Postgres; o teste roda em sqlite,
        // e em bancos vazios de teste nao ha duplicatas a eliminar)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DELETE FROM challenge_user a USING challenge_user b WHERE a.id > b.id AND a.user_id = b.user_id AND a.challenge_id = b.challenge_id');
        }

        Schema::table('challenge', function (Blueprint $table) {
            $table->index('type_encryption_id', 'challenge_type_encryption_id_index');
        });

        Schema::table('challenge_user', function (Blueprint $table) {
            $table->index('challenge_id', 'challenge_user_challenge_id_index');
            // atende filtros por usuario e o ORDER BY created_at do recent-activity
            $table->index(['user_id', 'created_at'], 'challenge_user_user_created_index');
            // integridade: cada usuario so tem um registro por desafio
            $table->unique(['user_id', 'challenge_id'], 'challenge_user_user_challenge_unique');
        });

        Schema::table('achievements_users', function (Blueprint $table) {
            $table->index('user_id', 'achievements_users_user_id_index');
            $table->index('achievement_id', 'achievements_users_achievement_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge', function (Blueprint $table) {
            $table->dropIndex('challenge_type_encryption_id_index');
        });

        Schema::table('challenge_user', function (Blueprint $table) {
            $table->dropUnique('challenge_user_user_challenge_unique');
            $table->dropIndex('challenge_user_user_created_index');
            $table->dropIndex('challenge_user_challenge_id_index');
        });

        Schema::table('achievements_users', function (Blueprint $table) {
            $table->dropIndex('achievements_users_user_id_index');
            $table->dropIndex('achievements_users_achievement_id_index');
        });
    }
};