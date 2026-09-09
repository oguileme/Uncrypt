<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->string('icon', 32)->after('required_count');
            $table->string('color', 32)->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color']);
        });
    }
};
