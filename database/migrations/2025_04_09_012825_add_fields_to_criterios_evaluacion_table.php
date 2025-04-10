<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            // Agregar campo descripcion si no existe
            if (!Schema::hasColumn('criterios_evaluacion', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
            
            // Agregar campo concurso_id si no existe
            if (!Schema::hasColumn('criterios_evaluacion', 'concurso_id')) {
                $table->foreignId('concurso_id')->after('puntaje_maximo')
                      ->constrained('concursos')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['concurso_id']);
            
            // Eliminar las columnas
            $table->dropColumn(['descripcion', 'concurso_id']);
        });
    }
};