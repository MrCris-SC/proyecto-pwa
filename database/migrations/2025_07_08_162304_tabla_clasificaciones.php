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
        Schema::create('clasificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('concurso_id');
            $table->string('equipo_id');
            $table->unsignedBigInteger('user_id'); // líder del equipo
            $table->string('fase', 20); // local, estatal, nacional
            $table->unsignedInteger('posicion'); // 1, 2, 3, etc.
            $table->string('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('concurso_id')->references('id')->on('concursos')->onDelete('cascade');
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['concurso_id', 'fase', 'posicion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificaciones');
    }
};
