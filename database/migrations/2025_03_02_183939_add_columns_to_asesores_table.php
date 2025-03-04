<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAsesoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asesores', function (Blueprint $table) {
            // Aquí defines las nuevas columnas
            $table->string('tipo_asesor')->after('equipo_id');
            $table->string('clave_presupuestal')->nullable()->after('tipo_asesor');
            $table->string('nivel_academico')->after('clave_presupuestal');
            $table->json('perfiles_jurado')->nullable()->after('nivel_academico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asesores', function (Blueprint $table) {
            // Aquí defines cómo revertir los cambios
            $table->dropColumn('tipo_asesor');
            $table->dropColumn('clave_presupuestal');
            $table->dropColumn('nivel_academico');
            $table->dropColumn('perfiles_jurado');
        });
    }
}