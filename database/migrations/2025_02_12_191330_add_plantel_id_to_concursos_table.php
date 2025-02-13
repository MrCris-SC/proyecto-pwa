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
            $table->unsignedBigInteger('plantel_id')->nullable();
            $table->foreign('plantel_id')->references('id_plantel')->on('planteles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concursos', function (Blueprint $table) {
            $table->dropForeign(['plantel_id']);
            $table->dropColumn('plantel_id');
        });
    }
};
