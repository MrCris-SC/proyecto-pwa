<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evaluadores', function (Blueprint $table) {
            $table->integer('userID')->nullable();
            $table->json('perfil')->nullable(); // Add a JSON column for multiple options
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluadores', function (Blueprint $table) {
            $table->dropColumn(['userID', 'perfil']);
        });
    }
};
