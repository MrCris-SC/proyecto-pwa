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
        Schema::table('resultados_finales', function (Blueprint $table) {
        $table->string('categoria')->nullable()->after('concurso_id');
        $table->unsignedBigInteger('modalidad_id')->nullable()->after('categoria');
        $table->enum('fase', ['local', 'estatal', 'nacional'])->default('local')->after('modalidad_id');
        $table->boolean('clasificado')->default(false)->after('fase');
        $table->integer('lugar')->nullable()->after('promedio_final');

        // FK opcionales
        
        $table->foreign('modalidad_id')->references('id')->on('modalidades')->onDelete('set null');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados_finales', function (Blueprint $table) {
            
            $table->dropForeign(['modalidad_id']);
            $table->dropColumn(['modalidad_id', 'fase', 'clasificado', 'lugar']);
        });
    }
};
