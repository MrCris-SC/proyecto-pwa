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
        if (!Schema::hasTable('planteles')) {
            Schema::create('planteles', function (Blueprint $table) {
                $table->id('id_plantel');
                $table->string('nombre_completo')->nullable();
                $table->string('nombre_corto');
                $table->string('direccion')->nullable();
                $table->string('telefono')->nullable();
                $table->string('correo')->unique()->nullable();
                $table->integer('estado_id'); // Ensure this matches the data type of idestado in estados table
                $table->foreign('estado_id')->references('idestado')->on('estados')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planteles');
    }
};
