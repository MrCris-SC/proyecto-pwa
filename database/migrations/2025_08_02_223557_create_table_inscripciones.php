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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('concurso_id')->constrained('concursos')->cascadeOnDelete();
            $table->string('equipo_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('estado', 50)->default('pendiente'); // pendiente, aprobado, rechazado, etc.
            $table->string('fase_origen', 50)->nullable(); // ej. 'local', 'estatal'
            $table->timestamps();

            $table->unique(['concurso_id', 'equipo_id']);
            // Si 'equipo_id' hace referencia a otra tabla y tienes modelo/PK, podrías agregar foreign key también:
            // $table->foreign('equipo_id')->references('id')->on('equipos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
