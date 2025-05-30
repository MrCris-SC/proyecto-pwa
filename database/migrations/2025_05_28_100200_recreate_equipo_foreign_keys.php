<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('asesores', function (Blueprint $table) {
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
        });
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
        });
        Schema::table('participantes', function (Blueprint $table) {
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('set null');
        });
        Schema::table('resultados_finales', function (Blueprint $table) {
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('asesores', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
        });
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
        });
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
        });
        Schema::table('resultados_finales', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
        });
    }
};
