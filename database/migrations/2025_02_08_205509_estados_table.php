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
        Schema::create('estados', function (Blueprint $table) {
            $table->integer('idestado')->primary();
            $table->string('clave', 10);
            $table->string('nombre', 254);
            $table->string('abreviatura', 20);
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
        });

        Schema::create('municipios', function (Blueprint $table) {
            $table->integer('idmunicipio')->primary();
            $table->integer('idestado');
            $table->string('clave', 20);
            $table->string('nombre', 180);
            $table->string('sigla', 20);
            $table->foreign('idestado')->references('idestado')->on('estados')->onUpdate('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('estados');
    }
};
