<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            // Agregar columna concurso_id como entero sin signo primero
            $table->unsignedBigInteger('concurso_id')->after('id');
            
            // Crear la restricción de clave foránea
            $table->foreign('concurso_id')
                  ->references('id')
                  ->on('concursos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['concurso_id']);
            
            // Eliminar la columna
            $table->dropColumn('concurso_id');
        });
    }
};
