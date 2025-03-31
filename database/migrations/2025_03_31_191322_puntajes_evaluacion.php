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
        Schema::create('puntajes_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluacion_id');
            $table->unsignedBigInteger('criterio_id');
            $table->decimal('puntaje_obtenido', 5, 2)->nullable();
            $table->text('comentario')->nullable(); // Opcional: Permite observaciones del evaluador
            $table->timestamps();

            $table->foreign('evaluacion_id')
                  ->references('id')
                  ->on('evaluaciones')
                  ->onDelete('cascade');

            $table->foreign('criterio_id')
                  ->references('id')
                  ->on('criterios_evaluacion')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntajes_evaluacion');
    }
};
