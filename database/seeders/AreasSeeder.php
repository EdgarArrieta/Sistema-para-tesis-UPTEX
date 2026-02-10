<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AreasSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'nombre' => 'Soporte Técnico',
                'descripcion' => 'Problemas de hardware, software y equipos de cómputo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Redes y Conectividad',
                'descripcion' => 'Problemas de internet, red WiFi y conexiones',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Sistemas',
                'descripcion' => 'Desarrollo y mantenimiento de sistemas institucionales',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Infraestructura',
                'descripcion' => 'Mantenimiento de servidores y datacenter',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Mesa de Ayuda',
                'descripcion' => 'Atención general y canalización de solicitudes',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('areas')->insert($areas);
    }
}