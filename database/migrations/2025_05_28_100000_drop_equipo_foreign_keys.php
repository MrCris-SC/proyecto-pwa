<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
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
    }

    public function down(): void
    {
        // No recrear aquí, se hace en la migración de recreación
    }
};
