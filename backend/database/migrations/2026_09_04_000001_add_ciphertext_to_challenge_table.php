<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenge', function (Blueprint $table) {
            $table->string('ciphertext')->nullable()->after('key');
        });
    }

    public function down(): void
    {
        Schema::table('challenge', function (Blueprint $table) {
            $table->dropColumn('ciphertext');
        });
    }
};