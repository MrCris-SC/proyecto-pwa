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
        Schema::table('evaluadores', function (Blueprint $table) {
            $table->dropColumn(['email', 'nombre', 'telefono', 'estado', 'password']);
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluadores', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('nombre')->nullable();
            $table->string('telefono')->nullable();
            $table->string('estado')->nullable();
            $table->string('password')->nullable();
           
        });
    }
};
