<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('admin'), // Contraseña encriptada
            'role' => 'admin', // Rol de administrador
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Operador user
        DB::table('users')->insert([
            'name' => 'Operador',
            'email' => 'operador@operador.operador',
            'password' => Hash::make('operador'),
            'role' => 'operador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}