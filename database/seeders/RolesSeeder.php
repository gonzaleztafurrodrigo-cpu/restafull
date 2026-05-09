<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrador'],
            ['name' => 'cashier', 'display_name' => 'Caja'],
            ['name' => 'waiter', 'display_name' => 'Mesero'],
            ['name' => 'delivery', 'display_name' => 'Domiciliario'],
            ['name' => 'client', 'display_name' => 'Cliente'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
