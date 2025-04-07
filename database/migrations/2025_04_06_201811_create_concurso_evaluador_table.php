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
        Schema::create('concurso_evaluador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concurso_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluador_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        
            $table->unique(['concurso_id', 'evaluador_id']); // evita inscripciones duplicadas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concurso_evaluador');
    }
};
