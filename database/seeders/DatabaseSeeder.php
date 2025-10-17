<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
        EstadosSeeder::class,
        LineasInvestigacionSeeder::class,
        ModalidadesSeeder::class,
        MunicipiosSeeder::class,
    ]); 
    }
}