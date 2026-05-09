<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class CreateTenantAdmin extends Command
{
    protected $signature = 'tenant:admin {tenant_id}';
    protected $description = 'Crea un usuario administrador para un tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error('Tenant no encontrado.');
            return;
        }

        $name = $this->ask('Nombre del administrador');
        $email = $this->ask('Email');
        $password = $this->secret('Contraseña');

        // Forzar conexión al tenant
        $dbName = 'tenant_' . $tenant->id;
        config(['database.connections.tenant.database' => $dbName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        $role = \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('roles')
            ->where('name', 'admin')
            ->first();

        if (!$role) {
            $this->error('Rol admin no encontrado.');
            return;
        }

        \Illuminate\Support\Facades\DB::connection('tenant')->table('tenant_users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Administrador '{$name}' creado correctamente para el restaurante '{$tenant->name}'.");
    }
}
