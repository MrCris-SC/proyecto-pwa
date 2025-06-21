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
        Schema::table('asesores', function (Blueprint $table) {
            // Elimina la foreign key constraint
            $table->dropForeign('asesores_equipo_id_foreign');
            // Elimina el índice unique existente
            $table->dropUnique('asesores_equipo_id_unique');
            // Crea el nuevo índice normal
            $table->index('equipo_id', 'asesores_equipo_id_foreign');
            // Vuelve a crear la foreign key constraint
            $table->foreign('equipo_id', 'asesores_equipo_id_foreign')->references('id')->on('equipos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asesores', function (Blueprint $table) {
            // Elimina la foreign key constraint
            $table->dropForeign('asesores_equipo_id_foreign');
            // Elimina el índice normal
            $table->dropIndex('asesores_equipo_id_foreign');
            // Vuelve a crear el índice unique
            $table->unique('equipo_id', 'asesores_equipo_id_unique');
            // Vuelve a crear la foreign key constraint
            $table->foreign('equipo_id', 'asesores_equipo_id_foreign')->references('id')->on('equipos');
        });
    }
};
