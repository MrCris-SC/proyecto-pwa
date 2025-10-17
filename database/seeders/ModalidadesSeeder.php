<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('modalidades')->insert([
            ['id' => 1, 'nombre' => 'Prototipos Tecnologicos', 'tipo' => 'Prototipos', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'nombre' => 'Prototipos Didacticos', 'tipo' => 'Prototipos', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'nombre' => 'Prototipos de Desarrollo de Software', 'tipo' => 'Prototipos', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'nombre' => 'Emprendedor Social', 'tipo' => 'Emprendimientos', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'nombre' => 'Emprendedor Verde', 'tipo' => 'Emprendimientos', 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'nombre' => 'Emprendedor en Tecnologias', 'tipo' => 'Emprendimientos', 'created_at' => null, 'updated_at' => null],
        ]);
    }
}
