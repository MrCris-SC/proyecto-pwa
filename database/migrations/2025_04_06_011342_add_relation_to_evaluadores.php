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
            $table->foreignId('evaluador_id')->constrained('users');
            $table->dropForeign(['asesor_id']);
            $table->dropColumn('asesor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->dropForeign(['evaluador_id']);
            $table->dropColumn('evaluador_id');
            $table->foreignId('asesor_id')->constrained('asesores');
        });
    }
};
