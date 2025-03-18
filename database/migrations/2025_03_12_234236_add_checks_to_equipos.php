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
        Schema::table('equipos', function (Blueprint $table) {
            $table->boolean('foapacheck')->default(false);
            $table->boolean(column: 'focomocheck')->default(false);
            $table->boolean(column: 'foascheck')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn('focomocheck');
            $table->dropColumn('foapacheck');
            $table->dropColumn('foascheck');

        });
    }
};
