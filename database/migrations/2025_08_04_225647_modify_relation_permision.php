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
        Schema::table('evaluaciones', function (Blueprint $table) {
            // Si no conoces el nombre exacto del constraint, puedes usar el array syntax:
            $table->dropForeign(['evaluador_id']);
            // Recrear con cascade
            $table->foreign('evaluador_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        
    }

    public function down(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            // Revertir: eliminar cascade y volver a restrict (o no action)
            $table->dropForeign(['evaluador_id']);
            $table->foreign('evaluador_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // o ->onDelete('no action') según como estaba antes
        });
    }
};
