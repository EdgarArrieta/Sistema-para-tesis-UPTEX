<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            // ADMINISTRADOR
            [
                'nombre' => 'Carlos',
                'apellido' => 'Administrador',
                'correo' => 'admin@uptex.edu.mx',
                'password' => Hash::make('admin123'),
                'id_rol' => 1,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // TÉCNICOS
            [
                'nombre' => 'María',
                'apellido' => 'Técnico Soporte',
                'correo' => 'maria.tecnico@uptex.edu.mx',
                'password' => Hash::make('tecnico123'),
                'id_rol' => 2,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Juan',
                'apellido' => 'Técnico Redes',
                'correo' => 'juan.tecnico@uptex.edu.mx',
                'password' => Hash::make('tecnico123'),
                'id_rol' => 2,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Técnico Sistemas',
                'correo' => 'pedro.tecnico@uptex.edu.mx',
                'password' => Hash::make('tecnico123'),
                'id_rol' => 2,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // USUARIOS NORMALES
            [
                'nombre' => 'Ana',
                'apellido' => 'García López',
                'correo' => 'ana.garcia@uptex.edu.mx',
                'password' => Hash::make('usuario123'),
                'id_rol' => 3,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Martínez Pérez',
                'correo' => 'luis.martinez@uptex.edu.mx',
                'password' => Hash::make('usuario123'),
                'id_rol' => 3,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Carmen',
                'apellido' => 'Rodríguez Silva',
                'correo' => 'carmen.rodriguez@uptex.edu.mx',
                'password' => Hash::make('usuario123'),
                'id_rol' => 3,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Hernández Cruz',
                'correo' => 'roberto.hernandez@uptex.edu.mx',
                'password' => Hash::make('usuario123'),
                'id_rol' => 3,
                'activo' => true,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('usuarios')->insert($usuarios);
    }
}