<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resultados_finales', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
        });
    }

    public function down(): void
    {
        Schema::table('resultados_finales', function (Blueprint $table) {
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
        });
    }
};
