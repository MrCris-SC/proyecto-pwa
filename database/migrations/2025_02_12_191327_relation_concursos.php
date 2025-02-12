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
        Schema::table('concursos', function (Blueprint $table) {
            $table->unsignedBigInteger('fase_id')->nullable();
            $table->foreign('fase_id')->references('id')->on('fases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concursos', function (Blueprint $table) {
            $table->dropForeign(['fase_id']);
            $table->dropColumn('fase_id');
        });
    }
};
