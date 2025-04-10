<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            $table->foreignId('linea_investigacion_id')
                  ->nullable()
                  ->constrained('lineas_investigacion') // Asegúrate de que esta tabla exista
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('criterios_evaluacion', function (Blueprint $table) {
            $table->dropForeign(['linea_investigacion_id']);
            $table->dropColumn('linea_investigacion_id');
        });
    }
};