<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TenantDataSeeder extends Seeder
{
    private function connectTenant(string $tenantId): void
    {
        $dbName = 'tenant_' . $tenantId;
        config(['database.connections.tenant.database' => $dbName]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
    }

    private function disconnectTenant(): void
    {
        DB::setDefaultConnection('central');
    }

    public function run(): void
    {
        $tenants = [
            '09a3c627-59ab-45bd-9135-e2473a36d2e6' => 'chamopizzero',
            '1362b47f-7065-428a-a4d7-842a412ba24d' => 'Sushi Express',
            '13818934-3ca8-40a3-9d20-03c7c0364a7c' => 'Chamo Burguer',
            '1430d9bb-71a1-4114-8734-8daa2a1190b4' => 'Tacos & Co',
            '57bfd3e7-1d64-41e1-9b16-b0260696f057' => 'Burger King Jr',
            '5aed78ad-88aa-4042-9452-ff93751cd32d' => 'pizzahoot',
            '738a7915-b6bb-4429-82d2-feec992d3333' => 'La Parrilla',
            'bf4fac9b-b896-43dd-934d-0418aacaab6c' => 'Pizza Palace',
        ];

        $menuData = [
            'chamopizzero' => [
                'categories' => ['Pizzas', 'Bebidas', 'Postres'],
                'products' => [
                    ['name' => 'Pizza Margarita', 'price' => 25000, 'category' => 'Pizzas'],
                    ['name' => 'Pizza Pepperoni', 'price' => 30000, 'category' => 'Pizzas'],
                    ['name' => 'Pizza BBQ', 'price' => 32000, 'category' => 'Pizzas'],
                    ['name' => 'Coca Cola', 'price' => 4000, 'category' => 'Bebidas'],
                    ['name' => 'Agua', 'price' => 2000, 'category' => 'Bebidas'],
                    ['name' => 'Tiramisú', 'price' => 12000, 'category' => 'Postres'],
                ],
            ],
            'Sushi Express' => [
                'categories' => ['Rolls', 'Nigiri', 'Bebidas'],
                'products' => [
                    ['name' => 'California Roll', 'price' => 28000, 'category' => 'Rolls'],
                    ['name' => 'Dragon Roll', 'price' => 35000, 'category' => 'Rolls'],
                    ['name' => 'Spicy Tuna Roll', 'price' => 30000, 'category' => 'Rolls'],
                    ['name' => 'Nigiri Salmón', 'price' => 15000, 'category' => 'Nigiri'],
                    ['name' => 'Nigiri Atún', 'price' => 15000, 'category' => 'Nigiri'],
                    ['name' => 'Sake', 'price' => 8000, 'category' => 'Bebidas'],
                    ['name' => 'Té Verde', 'price' => 5000, 'category' => 'Bebidas'],
                ],
            ],
            'Chamo Burguer' => [
                'categories' => ['Hamburguesas', 'Papas', 'Bebidas'],
                'products' => [
                    ['name' => 'Hamburguesa Clásica', 'price' => 18000, 'category' => 'Hamburguesas'],
                    ['name' => 'Hamburguesa BBQ', 'price' => 22000, 'category' => 'Hamburguesas'],
                    ['name' => 'Hamburguesa Doble', 'price' => 28000, 'category' => 'Hamburguesas'],
                    ['name' => 'Papas Fritas', 'price' => 8000, 'category' => 'Papas'],
                    ['name' => 'Papas con Queso', 'price' => 10000, 'category' => 'Papas'],
                    ['name' => 'Malteada Chocolate', 'price' => 9000, 'category' => 'Bebidas'],
                    ['name' => 'Jugo Natural', 'price' => 6000, 'category' => 'Bebidas'],
                ],
            ],
            'Tacos & Co' => [
                'categories' => ['Tacos', 'Burritos', 'Bebidas'],
                'products' => [
                    ['name' => 'Taco de Pollo', 'price' => 8000, 'category' => 'Tacos'],
                    ['name' => 'Taco de Carne', 'price' => 9000, 'category' => 'Tacos'],
                    ['name' => 'Taco Mixto', 'price' => 10000, 'category' => 'Tacos'],
                    ['name' => 'Burrito Clásico', 'price' => 18000, 'category' => 'Burritos'],
                    ['name' => 'Burrito Especial', 'price' => 22000, 'category' => 'Burritos'],
                    ['name' => 'Horchata', 'price' => 5000, 'category' => 'Bebidas'],
                    ['name' => 'Limonada', 'price' => 4000, 'category' => 'Bebidas'],
                ],
            ],
            'Burger King Jr' => [
                'categories' => ['Burgers', 'Combos', 'Bebidas'],
                'products' => [
                    ['name' => 'Whopper Jr', 'price' => 20000, 'category' => 'Burgers'],
                    ['name' => 'Cheeseburger', 'price' => 15000, 'category' => 'Burgers'],
                    ['name' => 'Combo Whopper', 'price' => 32000, 'category' => 'Combos'],
                    ['name' => 'Combo Chicken', 'price' => 28000, 'category' => 'Combos'],
                    ['name' => 'Coca Cola', 'price' => 4000, 'category' => 'Bebidas'],
                    ['name' => 'Sprite', 'price' => 4000, 'category' => 'Bebidas'],
                ],
            ],
            'pizzahoot' => [
                'categories' => ['Pizzas', 'Alitas', 'Bebidas'],
                'products' => [
                    ['name' => 'Pizza Hawaiana', 'price' => 28000, 'category' => 'Pizzas'],
                    ['name' => 'Pizza Suprema', 'price' => 35000, 'category' => 'Pizzas'],
                    ['name' => 'Pizza Veggie', 'price' => 27000, 'category' => 'Pizzas'],
                    ['name' => 'Alitas BBQ x6', 'price' => 22000, 'category' => 'Alitas'],
                    ['name' => 'Alitas Picantes x6', 'price' => 22000, 'category' => 'Alitas'],
                    ['name' => 'Pepsi', 'price' => 4000, 'category' => 'Bebidas'],
                    ['name' => 'Agua con Gas', 'price' => 3000, 'category' => 'Bebidas'],
                ],
            ],
            'La Parrilla' => [
                'categories' => ['Carnes', 'Acompañamientos', 'Bebidas'],
                'products' => [
                    ['name' => 'Churrasco', 'price' => 45000, 'category' => 'Carnes'],
                    ['name' => 'Costillas BBQ', 'price' => 55000, 'category' => 'Carnes'],
                    ['name' => 'Pollo a la Parrilla', 'price' => 32000, 'category' => 'Carnes'],
                    ['name' => 'Papas a la francesa', 'price' => 8000, 'category' => 'Acompañamientos'],
                    ['name' => 'Ensalada', 'price' => 7000, 'category' => 'Acompañamientos'],
                    ['name' => 'Cerveza', 'price' => 6000, 'category' => 'Bebidas'],
                    ['name' => 'Limonada', 'price' => 5000, 'category' => 'Bebidas'],
                ],
            ],
            'Pizza Palace' => [
                'categories' => ['Pizzas', 'Pastas', 'Bebidas'],
                'products' => [
                    ['name' => 'Pizza Cuatro Quesos', 'price' => 38000, 'category' => 'Pizzas'],
                    ['name' => 'Pizza Diavola', 'price' => 36000, 'category' => 'Pizzas'],
                    ['name' => 'Pizza Funghi', 'price' => 34000, 'category' => 'Pizzas'],
                    ['name' => 'Pasta Carbonara', 'price' => 28000, 'category' => 'Pastas'],
                    ['name' => 'Pasta Bolognesa', 'price' => 26000, 'category' => 'Pastas'],
                    ['name' => 'Vino Tinto', 'price' => 12000, 'category' => 'Bebidas'],
                    ['name' => 'Agua Mineral', 'price' => 3000, 'category' => 'Bebidas'],
                ],
            ],
        ];

        foreach ($tenants as $tenantId => $tenantName) {
            echo "Seeding {$tenantName}...\n";

            try {
                $this->connectTenant($tenantId);

                if (DB::table('categories')->count() > 0) {
                    echo "  → Limpiando datos anteriores...\n";
                    DB::table('order_items')->delete();
                    DB::table('orders')->delete();
                    DB::table('cash_registers')->delete();
                    DB::table('products')->delete();
                    DB::table('categories')->delete();
                    DB::table('tables')->delete();
                    DB::table('tenant_users')->whereIn('role_id', [2,3,4])->delete();
                }

                $data = $menuData[$tenantName] ?? $menuData['chamopizzero'];

                // Categorías
                $categoryIds = [];
                foreach ($data['categories'] as $i => $catName) {
                    $categoryIds[$catName] = DB::table('categories')->insertGetId([
                        'name' => $catName,
                        'is_active' => true,
                        'order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Productos
                $productIds = [];
                foreach ($data['products'] as $i => $prod) {
                    $productIds[$prod['name']] = DB::table('products')->insertGetId([
                        'name' => $prod['name'],
                        'price' => $prod['price'],
                        'category_id' => $categoryIds[$prod['category']],
                        'is_active' => true,
                        'order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Mesas
                $tableIds = [];
                for ($t = 1; $t <= 8; $t++) {
                    $tableIds[] = DB::table('tables')->insertGetId([
                        'name' => 'Mesa ' . $t,
                        'capacity' => 4,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Usuarios
                $roles = DB::table('roles')->pluck('id', 'name');
                $slug = strtolower(str_replace([' ', '&'], ['', ''], $tenantName));

                $userIds = [];
                foreach ([
                    ['name' => 'Cajero Principal', 'email' => "cajero@{$slug}.com", 'role' => 'cashier'],
                    ['name' => 'Mesero 1', 'email' => "mesero1@{$slug}.com", 'role' => 'waiter'],
                    ['name' => 'Domiciliario 1', 'email' => "domiciliario1@{$slug}.com", 'role' => 'delivery'],
                ] as $user) {
                    $userIds[$user['role']] = DB::table('tenant_users')->insertGetId([
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'password' => Hash::make('password123'),
                        'role_id' => $roles[$user['role']],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $cashierId = $userIds['cashier'];
                $waiterId = $userIds['waiter'];
                $deliveryId = $userIds['delivery'];

                // Pedidos últimos 3 meses
                $startDate = Carbon::now()->subMonths(3)->startOfDay();
                $endDate = Carbon::now();
                $orderNumber = 1;
                $products = array_values($productIds);

                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    $dailyOrders = rand(3, 8);
                    for ($o = 0; $o < $dailyOrders; $o++) {
                        $isDelivery = rand(0, 1);
                        $paymentMethod = rand(0, 1) ? 'cash' : 'transfer';
                        $orderTime = $currentDate->copy()->addHours(rand(10, 22))->addMinutes(rand(0, 59));

                        $orderId = DB::table('orders')->insertGetId([
                            'order_number' => 'ORD' . str_pad($orderNumber++, 5, '0', STR_PAD_LEFT),
                            'type' => $isDelivery ? 'delivery' : 'table',
                            'status' => 'delivered',
                            'payment_method' => $paymentMethod,
                            'payment_status' => 'verified',
                            'customer_name' => $isDelivery ? 'Cliente ' . rand(1, 100) : null,
                            'customer_phone' => $isDelivery ? '300' . rand(1000000, 9999999) : null,
                            'delivery_address' => $isDelivery ? 'Calle ' . rand(1, 100) . ' # ' . rand(1, 50) . '-' . rand(1, 99) : null,
                            'table_id' => !$isDelivery ? $tableIds[array_rand($tableIds)] : null,
                            'waiter_id' => !$isDelivery ? $waiterId : null,
                            'cashier_id' => $cashierId,
                            'delivery_id' => $isDelivery ? $deliveryId : null,
                            'subtotal' => 0,
                            'total' => 0,
                            'delivered_at' => $orderTime,
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ]);

                        $numItems = rand(1, 4);
                        $subtotal = 0;
                        $usedProducts = array_rand($products, min($numItems, count($products)));
                        if (!is_array($usedProducts)) $usedProducts = [$usedProducts];

                        foreach ($usedProducts as $productKey) {
                            $productName = array_keys($productIds)[$productKey];
                            $productId = $products[$productKey];
                            $price = $data['products'][array_search($productName, array_column($data['products'], 'name'))]['price'] ?? 10000;
                            $qty = rand(1, 3);
                            $itemSubtotal = $price * $qty;
                            $subtotal += $itemSubtotal;

                            DB::table('order_items')->insert([
                                'order_id' => $orderId,
                                'product_id' => $productId,
                                'quantity' => $qty,
                                'unit_price' => $price,
                                'subtotal' => $itemSubtotal,
                                'created_at' => $orderTime,
                                'updated_at' => $orderTime,
                            ]);
                        }

                        DB::table('orders')->where('id', $orderId)->update([
                            'subtotal' => $subtotal,
                            'total' => $subtotal,
                        ]);
                    }
                    $currentDate->addDay();
                }

                // Turnos de caja
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    $shiftStart = $currentDate->copy()->setHour(9)->setMinute(0);
                    $shiftEnd = $currentDate->copy()->setHour(22)->setMinute(0);

                    $totalCash = DB::table('orders')->where('status', 'delivered')->where('payment_method', 'cash')->whereBetween('delivered_at', [$shiftStart, $shiftEnd])->sum('total');
                    $totalTransfer = DB::table('orders')->where('status', 'delivered')->where('payment_method', 'transfer')->whereBetween('delivered_at', [$shiftStart, $shiftEnd])->sum('total');
                    $openingAmount = 100000;
                    $totalSales = $totalCash + $totalTransfer;
                    $expectedCash = $openingAmount + $totalCash;
                    $closingAmount = $expectedCash + rand(-5000, 5000);

                    DB::table('cash_registers')->insert([
                        'user_id' => $cashierId,
                        'opening_amount' => $openingAmount,
                        'closing_amount' => $closingAmount,
                        'total_cash' => $totalCash,
                        'total_transfer' => $totalTransfer,
                        'total_sales' => $totalSales,
                        'difference' => $closingAmount - $expectedCash,
                        'status' => 'closed',
                        'opened_at' => $shiftStart,
                        'closed_at' => $shiftEnd,
                        'created_at' => $shiftStart,
                        'updated_at' => $shiftEnd,
                    ]);

                    $currentDate->addDay();
                }

                echo "  ✓ {$tenantName} datos completados.\n";

            } catch (\Exception $e) {
                echo "  ✗ Error en {$tenantName}: {$e->getMessage()}\n";
            }

            $this->disconnectTenant();

            // Ciclos de facturación
            DB::setDefaultConnection('central');
            $tenant = DB::table('tenants')->where('id', $tenantId)->first();
            if (!$tenant) continue;

            DB::table('billing_cycles')->where('tenant_id', $tenantId)->delete();

            // 3 meses anteriores pagados
            for ($m = 3; $m >= 1; $m--) {
                $periodStart = Carbon::now()->subMonths($m)->startOfMonth()->toDateString();
                $periodEnd = Carbon::now()->subMonths($m)->endOfMonth()->toDateString();
                $paidDate = Carbon::now()->subMonths($m - 1)->startOfMonth()->addDays(rand(1, 5))->toDateString();

                try {
                    $this->connectTenant($tenantId);
                    $sales = DB::table('orders')->where('status', 'delivered')->whereBetween('delivered_at', [$periodStart . ' 00:00:00', $periodEnd . ' 23:59:59'])->sum('total');
                    $commission = $sales * ($tenant->commission_percentage / 100);
                    $this->disconnectTenant();
                } catch (\Exception $e) {
                    $sales = 0;
                    $commission = 0;
                }

                DB::table('billing_cycles')->insert([
                    'tenant_id' => $tenantId,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'sales_amount' => $sales,
                    'commission_amount' => $commission,
                    'monthly_fee' => $tenant->monthly_fee,
                    'total_amount' => $commission + $tenant->monthly_fee,
                    'status' => 'paid',
                    'due_date' => $periodEnd,
                    'paid_date' => $paidDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Ciclo actual pendiente
            try {
                $this->connectTenant($tenantId);
                $currentSales = DB::table('orders')->where('status', 'delivered')->whereMonth('delivered_at', now()->month)->whereYear('delivered_at', now()->year)->sum('total');
                $currentCommission = $currentSales * ($tenant->commission_percentage / 100);
                $this->disconnectTenant();
            } catch (\Exception $e) {
                $currentSales = 0;
                $currentCommission = 0;
            }

            DB::table('billing_cycles')->insert([
                'tenant_id' => $tenantId,
                'period_start' => Carbon::now()->startOfMonth()->toDateString(),
                'period_end' => Carbon::now()->endOfMonth()->toDateString(),
                'sales_amount' => $currentSales,
                'commission_amount' => $currentCommission,
                'monthly_fee' => $tenant->monthly_fee,
                'total_amount' => $currentCommission + $tenant->monthly_fee,
                'status' => 'pending',
                'due_date' => Carbon::now()->endOfMonth()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tenants')->where('id', $tenantId)->update([
                'billing_start_date' => Carbon::now()->subMonths(3)->startOfMonth()->toDateString(),
                'next_billing_date' => Carbon::now()->endOfMonth()->toDateString(),
                'last_paid_date' => Carbon::now()->startOfMonth()->toDateString(),
                'billing_status' => 'active',
            ]);

            echo "  ✓ Ciclos de {$tenantName} generados.\n";
        }

        echo "\n✅ Seeder completado.\n";
    }
}
