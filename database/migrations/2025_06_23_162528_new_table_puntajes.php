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
        Schema::create('puntajes_parciales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluacion_id')->unique();
            $table->string('equipo_id'); // Cambiado a string
            $table->unsignedBigInteger('concurso_id');
            $table->float('puntaje_total');
            $table->timestamps();

            $table->foreign('evaluacion_id')->references('id')->on('evaluaciones')->onDelete('cascade');
            $table->foreign('concurso_id')->references('id')->on('concursos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntajes_parciales');
    }
};
    
