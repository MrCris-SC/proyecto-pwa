<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estados')->insert([
            ['idestado' => 1, 'clave' => '01', 'nombre' => 'AGUASCALIENTES', 'abreviatura' => 'AGS.'],
            ['idestado' => 2, 'clave' => '02', 'nombre' => 'BAJA CALIFORNIA', 'abreviatura' => 'BC'],
            ['idestado' => 3, 'clave' => '03', 'nombre' => 'BAJA CALIFORNIA SUR', 'abreviatura' => 'BCS'],
            ['idestado' => 4, 'clave' => '04', 'nombre' => 'CAMPECHE', 'abreviatura' => 'CAMP.'],
            ['idestado' => 5, 'clave' => '05', 'nombre' => 'COAHUILA DE ZARAGOZA', 'abreviatura' => 'COAH.'],
            ['idestado' => 6, 'clave' => '06', 'nombre' => 'COLIMA', 'abreviatura' => 'COL.'],
            ['idestado' => 7, 'clave' => '07', 'nombre' => 'CHIAPAS', 'abreviatura' => 'CHIS.'],
            ['idestado' => 8, 'clave' => '08', 'nombre' => 'CHIHUAHUA', 'abreviatura' => 'CHIH.'],
            ['idestado' => 9, 'clave' => '09', 'nombre' => 'DISTRITO FEDERAL', 'abreviatura' => 'DF'],
            ['idestado' => 10, 'clave' => '10', 'nombre' => 'DURANGO', 'abreviatura' => 'DGO.'],
            ['idestado' => 11, 'clave' => '11', 'nombre' => 'GUANAJUATO', 'abreviatura' => 'GTO.'],
            ['idestado' => 12, 'clave' => '12', 'nombre' => 'GUERRERO', 'abreviatura' => 'GRO.'],
            ['idestado' => 13, 'clave' => '13', 'nombre' => 'HIDALGO', 'abreviatura' => 'HGO.'],
            ['idestado' => 14, 'clave' => '14', 'nombre' => 'JALISCO', 'abreviatura' => 'JAL.'],
            ['idestado' => 15, 'clave' => '15', 'nombre' => 'MEXICO', 'abreviatura' => 'MEX.'],
            ['idestado' => 16, 'clave' => '16', 'nombre' => 'MICHOACAN DE OCAMPO', 'abreviatura' => 'MICH.'],
            ['idestado' => 17, 'clave' => '17', 'nombre' => 'MORELOS', 'abreviatura' => 'MOR.'],
            ['idestado' => 18, 'clave' => '18', 'nombre' => 'NAYARIT', 'abreviatura' => 'NAY.'],
            ['idestado' => 19, 'clave' => '19', 'nombre' => 'NUEVO LEON', 'abreviatura' => 'NL'],
            ['idestado' => 20, 'clave' => '20', 'nombre' => 'OAXACA', 'abreviatura' => 'OAX.'],
            ['idestado' => 21, 'clave' => '21', 'nombre' => 'PUEBLA', 'abreviatura' => 'PUE.'],
            ['idestado' => 22, 'clave' => '22', 'nombre' => 'QUERETARO', 'abreviatura' => 'QRO.'],
            ['idestado' => 23, 'clave' => '23', 'nombre' => 'QUINTANA ROO', 'abreviatura' => 'Q. ROO'],
            ['idestado' => 24, 'clave' => '24', 'nombre' => 'SAN LUIS POTOSI', 'abreviatura' => 'SLP'],
            ['idestado' => 25, 'clave' => '25', 'nombre' => 'SINALOA', 'abreviatura' => 'SIN.'],
            ['idestado' => 26, 'clave' => '26', 'nombre' => 'SONORA', 'abreviatura' => 'SON.'],
            ['idestado' => 27, 'clave' => '27', 'nombre' => 'TABASCO', 'abreviatura' => 'TAB.'],
            ['idestado' => 28, 'clave' => '28', 'nombre' => 'TAMAULIPAS', 'abreviatura' => 'TAMPS.'],
            ['idestado' => 29, 'clave' => '29', 'nombre' => 'TLAXCALA', 'abreviatura' => 'TLAX.'],
            ['idestado' => 30, 'clave' => '30', 'nombre' => 'VERACRUZ DE IGNACIO DE LA LLAVE', 'abreviatura' => 'VER.'],
            ['idestado' => 31, 'clave' => '31', 'nombre' => 'YUCATAN', 'abreviatura' => 'YUC.'],
            ['idestado' => 32, 'clave' => '32', 'nombre' => 'ZACATECAS', 'abreviatura' => 'ZAC.'],
        ]);
    }
}
