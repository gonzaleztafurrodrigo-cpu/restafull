<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Pizza Palace',
                'email' => 'admin@pizzapalace.com',
                'domain' => 'pizzapalace.localhost',
                'commission_percentage' => 2,
                'plan' => 'pro',
                'monthly_fee' => 15000,
            ],
            [
                'name' => 'Burger King Jr',
                'email' => 'admin@burgerkingj.com',
                'domain' => 'burgerkingj.localhost',
                'commission_percentage' => 2,
                'plan' => 'basic',
                'monthly_fee' => 0,
            ],
            [
                'name' => 'Sushi Express',
                'email' => 'admin@sushiexpress.com',
                'domain' => 'sushiexpress.localhost',
                'commission_percentage' => 2,
                'plan' => 'pro',
                'monthly_fee' => 15000,
            ],
            [
                'name' => 'Tacos & Co',
                'email' => 'admin@tacosco.com',
                'domain' => 'tacosco.localhost',
                'commission_percentage' => 2,
                'plan' => 'basic',
                'monthly_fee' => 0,
            ],
            [
                'name' => 'La Parrilla',
                'email' => 'admin@laparrilla.com',
                'domain' => 'laparrilla.localhost',
                'commission_percentage' => 2.5,
                'plan' => 'pro',
                'monthly_fee' => 15000,
            ],
        ];

        foreach ($tenants as $data) {
            // Verificar si ya existe
            if (Tenant::where('email', $data['email'])->exists()) {
                echo "Skipping {$data['name']} — ya existe.\n";
                continue;
            }

            $billingStartDate = now()->toDateString();
            $nextBillingDate = now()->endOfMonth()->toDateString();

            $tenant = Tenant::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'domain' => $data['domain'],
                'commission_percentage' => $data['commission_percentage'],
                'is_active' => true,
                'plan' => $data['plan'],
                'monthly_fee' => $data['monthly_fee'],
                'billing_status' => 'active',
                'billing_start_date' => $billingStartDate,
                'next_billing_date' => $nextBillingDate,
                'last_paid_date' => null,
            ]);

            $tenant->domains()->create([
                'domain' => $data['domain'],
            ]);

            // Ciclo de facturación
            DB::table('billing_cycles')->insert([
                'tenant_id' => $tenant->id,
                'period_start' => $billingStartDate,
                'period_end' => $nextBillingDate,
                'sales_amount' => 0,
                'commission_amount' => 0,
                'monthly_fee' => $data['monthly_fee'],
                'total_amount' => $data['monthly_fee'],
                'status' => 'pending',
                'due_date' => $nextBillingDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Crear DB del tenant
            $dbName = 'tenant_' . $tenant->id;

            try {
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                config(['database.connections.tenant.database' => $dbName]);
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');

                Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--database' => 'tenant',
                    '--force' => true,
                ]);

                DB::table('settings')->insert([
                    ['key' => 'restaurant_name', 'value' => $data['name'], 'created_at' => now(), 'updated_at' => now()],
                    ['key' => 'restaurant_email', 'value' => $data['email'], 'created_at' => now(), 'updated_at' => now()],
                    ['key' => 'commission_percentage', 'value' => $data['commission_percentage'], 'created_at' => now(), 'updated_at' => now()],
                    ['key' => 'delivery_time', 'value' => '30-45', 'created_at' => now(), 'updated_at' => now()],
                    ['key' => 'min_order', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
                    ['key' => 'delivery_cost', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
                ]);

                DB::table('roles')->insert([
                    ['name' => 'admin', 'display_name' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'cashier', 'display_name' => 'Cajero', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'waiter', 'display_name' => 'Mesero', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'delivery', 'display_name' => 'Domiciliario', 'created_at' => now(), 'updated_at' => now()],
                ]);

                $adminRole = DB::table('roles')->where('name', 'admin')->first();
                DB::table('tenant_users')->insert([
                    'name' => 'Administrador',
                    'email' => $data['email'],
                    'password' => Hash::make('password123'),
                    'role_id' => $adminRole->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::setDefaultConnection('central');

                echo "✓ {$data['name']} creado correctamente.\n";

            } catch (\Exception $e) {
                $tenant->delete();
                echo "✗ Error creando {$data['name']}: {$e->getMessage()}\n";
            }
        }
    }
}
