<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('concurso_registrado_id')->nullable()->after('rol');
            // Si tienes la tabla de concursos y quieres agregar la FK:
            $table->foreign('concurso_registrado_id')->references('id')->on('concursos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['concurso_registrado_id']);
            $table->dropColumn('concurso_registrado_id');
        });
    }
};
