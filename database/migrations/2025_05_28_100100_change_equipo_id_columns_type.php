<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->string('id', 50)->change();
        });
        Schema::table('asesores', function (Blueprint $table) {
            $table->string('equipo_id', 50)->change();
        });
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->string('equipo_id', 50)->change();
        });
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('equipo_id', 50)->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('equipo_id', 50)->nullable()->change();
        });
        Schema::table('resultados_finales', function (Blueprint $table) {
            $table->string('equipo_id', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
        Schema::table('asesores', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->change();
        });
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->change();
        });
        Schema::table('participantes', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->nullable()->change();
        });
        Schema::table('resultados_finales', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->change();
        });
    }
};
