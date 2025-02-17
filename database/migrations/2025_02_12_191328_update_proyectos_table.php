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
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropForeign(['fase_id']);
            $table->dropColumn('fase_id');
            if (!Schema::hasColumn('proyectos', 'concurso_id')) {
                $table->foreignId('concurso_id')->constrained('concursos')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropForeign(['concurso_id']);
            $table->dropColumn('concurso_id');
            $table->foreignId('fase_id')->constrained('fases')->onDelete('cascade');
        });
    }
};
