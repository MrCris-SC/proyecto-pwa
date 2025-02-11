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
        Schema::table('concursos', function (Blueprint $table) {
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_terminacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concursos', function (Blueprint $table) {
            $table->dropColumn(['fecha_apertura', 'fecha_inicio', 'fecha_terminacion']);
        });
    }
};
