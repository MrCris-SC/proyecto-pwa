<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class ModifyCriteriosEvaluacionTable extends Migration
{
    public function up()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            // Primero eliminar la restricción de clave foránea si existe
            if (Schema::hasColumn('criterios_evaluacion', 'linea_investigacion_id')) {
                $table->dropForeign(['linea_investigacion_id']);
            }
            
            // Luego eliminar las columnas
            $table->dropColumn(['descripcion', 'linea_investigacion_id']);
            
            // Agregar nuevas columnas necesarias
            $table->foreignId('modalidad_id')->nullable()->constrained('modalidades');
            $table->enum('tipo_criterio', [
                'informe', 
                'modalidad', 
                'exposicion'
            ])->after('modalidad_id');
        });
    }

    public function down()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            // Revertir los cambios
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('linea_investigacion_id')->nullable();
            
            $table->dropForeign(['modalidad_id']);
            $table->dropColumn(['modalidad_id', 'tipo_criterio']);
        });
    }
}