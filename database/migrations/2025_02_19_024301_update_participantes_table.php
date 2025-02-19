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
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('nombre')->change(); // Mantiene `nombre` obligatorio
            $table->string('correo')->unique()->nullable(); // Campo de correo único y opcional
            $table->string('telefono')->nullable(); // Campo opcional
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn(['correo', 'telefono', 'proyecto_id']);
        });
    }
};
