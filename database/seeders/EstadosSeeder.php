<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadosSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            [
                'nombre' => 'Abierto',
                'tipo' => 'abierto',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'En Proceso',
                'tipo' => 'en_proceso',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Pendiente',
                'tipo' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Resuelto',
                'tipo' => 'resuelto',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Cerrado',
                'tipo' => 'cerrado',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Cancelado',
                'tipo' => 'cancelado',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('estados')->insert($estados);
    }
}