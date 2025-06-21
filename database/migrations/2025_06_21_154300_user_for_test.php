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
        // Crea 20 usuarios con rol 'lider'
        $lideres = [];
        for ($i = 1; $i <= 20; $i++) {
            $lideres[] = [
                'name' => 'Lider ' . $i,
                'email' => 'lider' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'rol' => 'lider',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insertOrIgnore($lideres);

        // Crea 10 usuarios con rol 'evaluador'
        $evaluadores = [];
        for ($i = 1; $i <= 10; $i++) {
            $evaluadores[] = [
                'name' => 'Evaluador ' . $i,
                'email' => 'evaluador' . $i . '@example.com',
                'password' => Hash::make('password123'),
                'rol' => 'evaluador',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insertOrIgnore($evaluadores);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina los usuarios lider creados por esta migración
        for ($i = 1; $i <= 20; $i++) {
            DB::table('users')->where('email', 'lider' . $i . '@example.com')->delete();
        }
        // Elimina los usuarios evaluador creados por esta migración
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->where('email', 'evaluador' . $i . '@example.com')->delete();
        }
    }
};
