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
        Schema::create('documentos_asesores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesor_id')->constrained('asesores')->onDelete('cascade');
            $table->string('ruta_documento'); // Ruta del archivo en S3
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
