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
        Schema::table('modalidades', function (Blueprint $table) {
            $table->dropForeign(['concurso_id']);
            $table->dropColumn('concurso_id');
            $table->string('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modalidades', function (Blueprint $table) {
            $table->foreignId('concurso_id')->constrained('concursos')->onDelete('cascade');
            $table->dropColumn('tipo');
        });
    }
};
