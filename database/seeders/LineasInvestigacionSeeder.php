<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineasInvestigacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lineas_investigacion')->insert([
            ['id' => 1, 'nombre' => 'Desarrollo Tecnologico', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'nombre' => 'Investigacion Educativa', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'nombre' => 'Desarrollo Sustentable y Medio ambiente', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'nombre' => 'Investigacion en ciencias de la salud', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'nombre' => 'Desarrollo humano, social y emocional', 'created_at' => null, 'updated_at' => null],
        ]);
    }
}

