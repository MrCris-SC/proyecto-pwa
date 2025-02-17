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
            if (!Schema::hasColumn('concursos', 'fase')) {
                $table->string('fase')->nullable();
            }
            if (Schema::hasColumn('concursos', 'fase_id')) {
                $table->dropForeign(['fase_id']);
                $table->dropColumn('fase_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concursos', function (Blueprint $table) {
            if (!Schema::hasColumn('concursos', 'fase_id')) {
                $table->unsignedBigInteger('fase_id');
                $table->foreign('fase_id')->references('id')->on('fases');
            }
            if (Schema::hasColumn('concursos', 'fase')) {
                $table->dropColumn('fase');
            }
        });
    }
};
