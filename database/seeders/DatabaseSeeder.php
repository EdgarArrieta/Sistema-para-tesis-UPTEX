<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            AreasSeeder::class,
            PrioridadesSeeder::class,
            EstadosSeeder::class,
            UsuariosSeeder::class,
        ]);

        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('📋 CREDENCIALES DE PRUEBA:');
        $this->command->info('');
        $this->command->info('👤 ADMINISTRADOR:');
        $this->command->info('   Email: admin@uptex.edu.mx');
        $this->command->info('   Password: admin123');
        $this->command->info('');
        $this->command->info('🔧 TÉCNICO:');
        $this->command->info('   Email: maria.tecnico@uptex.edu.mx');
        $this->command->info('   Password: tecnico123');
        $this->command->info('');
        $this->command->info('👥 USUARIO NORMAL:');
        $this->command->info('   Email: ana.garcia@uptex.edu.mx');
        $this->command->info('   Password: usuario123');
    }
}