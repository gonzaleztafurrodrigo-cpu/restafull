<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class OrderController extends Controller
{
    private function generateOrderNumber(): string
    {
        $last = DB::table('orders')->orderBy('id', 'desc')->first();
        $number = $last ? (int)substr($last->order_number, 3) + 1 : 1;
        return 'ORD' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    private function getTenantId(): string
    {
        return tenancy()->tenant->id ?? '';
    }

    private function broadcastEvent(string $channel, string $event, array $data): void
    {
        $pusher = new Pusher(
            config('broadcasting.connections.reverb.key'),
            config('broadcasting.connections.reverb.secret'),
            config('broadcasting.connections.reverb.app_id'),
            [
                'host' => config('broadcasting.connections.reverb.options.host'),
                'port' => config('broadcasting.connections.reverb.options.port'),
                'scheme' => config('broadcasting.connections.reverb.options.scheme'),
                'useTLS' => false,
            ]
        );
        $pusher->trigger($channel, $event, $data);
    }

    public function waiterIndex()
    {
        $tables = DB::table('tables')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $activeOrders = DB::table('orders')
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->where('type', 'table')
            ->get();

        $activeOrdersByTable = $activeOrders->keyBy('table_id');

        foreach ($activeOrdersByTable as $order) {
            $order->items = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('order_items.*', 'products.name as product_name', 'categories.name as category_name')
                ->where('order_items.order_id', $order->id)
                ->orderBy('categories.name')
                ->get();
        }

        $activeTableIds = $activeOrders->pluck('table_id')->toArray();

        return view('tenant.mesero.index', compact('tables', 'activeOrdersByTable', 'activeTableIds'));
    }

    public function waiterCreate(Request $request)
    {
        $tableId = $request->query('mesa');
        $table = DB::table('tables')->where('id', $tableId)->first();

        if (!$table) {
            return redirect()->route('tenant.mesero');
        }

        $activeOrder = DB::table('orders')
            ->where('table_id', $tableId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->first();

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($categories as $category) {
            $category->products = DB::table('products')
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }

        return view('tenant.mesero.create', compact('table', 'categories', 'activeOrder'));
    }

    public function waiterStore(Request $request)
    {
        $request->validate([
            'table_id' => 'required',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $waiterId = session('tenant_user')['id'];

        $activeOrder = DB::table('orders')
            ->where('table_id', $request->table_id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->first();

        if ($activeOrder) {
            $orderId = $activeOrder->id;
        } else {
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $this->generateOrderNumber(),
                'type' => 'table',
                'status' => 'pending',
                'table_id' => $request->table_id,
                'waiter_id' => $waiterId,
                'notes' => $request->notes,
                'subtotal' => 0,
                'total' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($request->items as $item) {
            $product = DB::table('products')->where('id', $item['product_id'])->first();
            $itemSubtotal = $product->price * $item['quantity'];

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $itemSubtotal,
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $currentTotal = DB::table('order_items')
            ->where('order_id', $orderId)
            ->sum('subtotal');

        DB::table('orders')->where('id', $orderId)->update([
            'subtotal' => $currentTotal,
            'total' => $currentTotal,
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $orderId)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'new.order', [
            'order_id' => $orderId,
            'order_number' => $order->order_number,
            'type' => 'table',
            'table_id' => $request->table_id,
        ]);

        return redirect()->route('tenant.mesero')
            ->with('success', 'Pedido enviado correctamente.');
    }

    public function clientMenu()
    {
        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($categories as $category) {
            $category->products = DB::table('products')
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }

        $settings = DB::table('settings')
            ->pluck('value', 'key')
            ->toArray();

        $customer = session('customer');

        $defaultAddress = null;
        if ($customer) {
            $defaultAddress = DB::table('customer_addresses')
                ->where('customer_id', $customer['id'])
                ->where('is_default', true)
                ->first();

            $addresses = DB::table('customer_addresses')
                ->where('customer_id', $customer['id'])
                ->orderBy('is_default', 'desc')
                ->get();
        } else {
            $addresses = collect();
        }

        $deliveryCost = (int)($settings['delivery_cost'] ?? 0);

        return view('tenant.client.menu', compact('categories', 'settings', 'customer', 'defaultAddress', 'addresses', 'deliveryCost'));
    }

    public function clientStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|in:cash,transfer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $customer = session('customer');

        $orderId = DB::table('orders')->insertGetId([
            'order_number' => $this->generateOrderNumber(),
            'type' => 'delivery',
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'customer_id' => $customer['id'] ?? null,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'delivery_address' => $request->delivery_address,
            'notes' => $request->notes,
            'subtotal' => 0,
            'total' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = DB::table('products')->where('id', $item['product_id'])->first();
            $itemSubtotal = $product->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $itemSubtotal,
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $deliveryCost = (int)(DB::table('settings')->where('key', 'delivery_cost')->value('value') ?? 0);
        $total = $subtotal + $deliveryCost;

        DB::table('orders')->where('id', $orderId)->update([
            'subtotal' => $subtotal,
            'total' => $total,
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $orderId)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'new.order', [
            'order_id' => $orderId,
            'order_number' => $order->order_number,
            'type' => 'delivery',
            'customer_name' => $request->customer_name,
        ]);

        $settings = DB::table('settings')
            ->pluck('value', 'key')
            ->toArray();

        $redirectRoute = $customer ? route('tenant.client.dashboard') : route('tenant.welcome');

        return view('tenant.client.order_confirmed', compact('order', 'settings', 'redirectRoute'));
    }

    public function cashierIndex()
    {
        $userId = session('tenant_user')['id'];

        // Obtener turno activo
        $openShift = DB::table('cash_registers')
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        $orders = DB::table('orders')
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'dispatched', 'collecting'])
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($orders as $order) {
            $order->items = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('order_items.*', 'products.name as product_name', 'categories.name as category_name')
                ->where('order_items.order_id', $order->id)
                ->get();

            if ($order->table_id) {
                $order->table = DB::table('tables')->where('id', $order->table_id)->first();
            }
        }

        $deliveryUsers = DB::table('tenant_users')
            ->join('roles', 'tenant_users.role_id', '=', 'roles.id')
            ->where('roles.name', 'delivery')
            ->where('tenant_users.is_active', true)
            ->select('tenant_users.id', 'tenant_users.name')
            ->get();

        // Pedidos completados del turno
        $completedOrders = collect();
        // Pedidos cancelados del turno
        $cancelledOrders = collect();
        if ($openShift) {
            $cancelledOrders = DB::table('orders')
                ->where('status', 'cancelled')
                ->where('updated_at', '>=', $openShift->opened_at)
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($cancelledOrders as $order) {
                $order->items = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->select('order_items.*', 'products.name as product_name', 'categories.name as category_name')
                    ->where('order_items.order_id', $order->id)
                    ->orderBy('categories.name')
                    ->get();

                if ($order->table_id) {
                    $order->table = DB::table('tables')->where('id', $order->table_id)->first();
                }
            }
        }
        $completedPage = request()->get('completed_page', 1);
        $completedPerPage = 5;
        $completedTotal = 0;

        if ($openShift) {
            $completedTotal = DB::table('orders')
                ->where('status', 'delivered')
                ->where('updated_at', '>=', $openShift->opened_at)
                ->count();

            $completedOrders = DB::table('orders')
                ->where('status', 'delivered')
                ->where('updated_at', '>=', $openShift->opened_at)
                ->orderBy('updated_at', 'desc')
                ->skip(($completedPage - 1) * $completedPerPage)
                ->take($completedPerPage)
                ->get();
        }

        $completedTotalPages = ceil($completedTotal / $completedPerPage);

        // Métricas del turno
        $shiftStats = null;
        if ($openShift) {
            $shiftStart = $openShift->opened_at;

            $shiftStats = [
                'new' => DB::table('orders')
                    ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
                    ->where('created_at', '>=', $shiftStart)
                    ->count(),
                'collecting' => DB::table('orders')
                    ->whereIn('status', ['dispatched', 'collecting'])
                    ->where('created_at', '>=', $shiftStart)
                    ->count(),
                'delivered' => DB::table('orders')
                    ->where('status', 'delivered')
                    ->where('updated_at', '>=', $shiftStart)
                    ->count(),
                'total_sales' => DB::table('orders')
                    ->where('status', 'delivered')
                    ->where('updated_at', '>=', $shiftStart)
                    ->sum('total'),
            ];
        }

        return view('tenant.caja.index', compact(
            'orders',
            'deliveryUsers',
            'openShift',
            'shiftStats',
            'completedOrders',
            'completedPage',
            'completedTotalPages',
            'completedTotal',
            'cancelledOrders'
        ));
    }

    public function cashierConfirm($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'status' => 'confirmed',
            'cashier_id' => session('tenant_user')['id'],
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => 'confirmed',
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function cashierReady($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'status' => 'ready',
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => 'ready',
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function cashierVerifyPayment($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'payment_status' => 'verified',
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => $order->status,
            'payment_status' => 'verified',
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function cashierDispatch(Request $request, $id)
    {
        $request->validate([
            'delivery_id' => 'required',
        ]);

        DB::table('orders')->where('id', $id)->update([
            'status' => 'dispatched',
            'delivery_id' => $request->delivery_id,
            'dispatched_at' => now(),
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenant = tenancy()->tenant;
        $tenantId = $this->getTenantId();

        // Solo registrar transacción si el pago fue por transferencia verificada
        if ($order->payment_method === 'transfer' && $order->payment_status === 'verified') {
            DB::table('transactions')->insert([
                'order_id' => $id,
                'order_total' => $order->total,
                'commission_percentage' => $tenant->commission_percentage,
                'commission_amount' => $order->total * ($tenant->commission_percentage / 100),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => 'dispatched',
            'order_number' => $order->order_number,
            'delivery_id' => $request->delivery_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function cashierCollect($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenant = tenancy()->tenant;
        $tenantId = $this->getTenantId();

        // Registrar transacción para efectivo al confirmar recaudo
        if ($order->payment_method === 'cash') {
            DB::table('transactions')->insert([
                'order_id' => $id,
                'order_total' => $order->total,
                'commission_percentage' => $tenant->commission_percentage,
                'commission_amount' => $order->total * ($tenant->commission_percentage / 100),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => 'delivered',
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function cashierClose($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenant = tenancy()->tenant;
        $tenantId = $this->getTenantId();

        DB::table('transactions')->insert([
            'order_id' => $id,
            'order_total' => $order->total,
            'commission_percentage' => $tenant->commission_percentage,
            'commission_amount' => $order->total * ($tenant->commission_percentage / 100),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => 'delivered',
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function deliveryIndex()
    {
        $userId = session('tenant_user')['id'];
        $period = request()->get('period', 'today');
        $page = request()->get('page', 1);
        $perPage = 10;

        $startDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };

        // Pedidos activos
        $activeOrders = DB::table('orders')
            ->where('delivery_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'dispatched')
                    ->orWhere(function ($q) {
                        $q->where('status', 'collecting')
                            ->where('payment_method', 'cash');
                    });
            })
            ->orderBy('dispatched_at', 'asc')
            ->get();

        // Pedidos completados con filtro
        $totalCompleted = DB::table('orders')
            ->where('delivery_id', $userId)
            ->where('status', 'delivered')
            ->where('updated_at', '>=', $startDate)
            ->count();

        $completedOrders = DB::table('orders')
            ->where('delivery_id', $userId)
            ->where('status', 'delivered')
            ->where('updated_at', '>=', $startDate)
            ->orderBy('updated_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $totalPages = ceil($totalCompleted / $perPage);

        // Totales del período
        $totalEarnings = DB::table('orders')
            ->where('delivery_id', $userId)
            ->where('status', 'delivered')
            ->where('updated_at', '>=', $startDate)
            ->sum('total');

        return view('tenant.domiciliario.index', compact(
            'activeOrders',
            'completedOrders',
            'period',
            'page',
            'totalPages',
            'totalCompleted',
            'totalEarnings'
        ));
    }

    public function deliveryComplete($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        $tenantId = $this->getTenantId();

        // Si es transferencia ya está pagado, se cierra directamente
        // Si es efectivo pasa a collecting para que caja recaude
        $newStatus = $order->payment_method === 'transfer' ? 'delivered' : 'collecting';

        DB::table('orders')->where('id', $id)->update([
            'status' => $newStatus,
            'delivered_at' => now(),
            'updated_at' => now(),
        ]);

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => $newStatus,
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function cashierEdit($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        $order->items = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('order_items.*', 'products.name as product_name', 'categories.name as category_name')
            ->where('order_items.order_id', $order->id)
            ->orderBy('categories.name')
            ->get();

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($categories as $category) {
            $category->products = DB::table('products')
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }

        if ($order->table_id) {
            $order->table = DB::table('tables')->where('id', $order->table_id)->first();
        }

        return view('tenant.caja.edit', compact('order', 'categories'));
    }

    public function cashierUpdate(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Eliminar items anteriores
        DB::table('order_items')->where('order_id', $id)->delete();

        // Insertar nuevos items
        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = DB::table('products')->where('id', $item['product_id'])->first();
            $itemSubtotal = $product->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            DB::table('order_items')->insert([
                'order_id' => $id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $itemSubtotal,
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('orders')->where('id', $id)->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'notes' => $request->notes,
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => $order->status,
            'order_number' => $order->order_number,
        ]);

        return redirect()->route('tenant.caja')
            ->with('success', 'Pedido actualizado correctamente.');
    }

    public function cashierArqueo()
    {
        $today = now()->startOfDay();

        // Pedidos cerrados hoy
        $ordersTable = DB::table('orders')
            ->where('status', 'delivered')
            ->where('type', 'table')
            ->where('delivered_at', '>=', $today)
            ->get();

        $ordersDelivery = DB::table('orders')
            ->where('status', 'delivered')
            ->where('type', 'delivery')
            ->where('delivered_at', '>=', $today)
            ->get();

        // Pedidos pendientes de recaudo
        $ordersPending = DB::table('orders')
            ->whereIn('status', ['dispatched', 'collecting'])
            ->where('type', 'delivery')
            ->get();

        // Totales
        $totalTable = $ordersTable->sum('total');
        $totalDeliveryCash = $ordersDelivery->where('payment_method', 'cash')->sum('total');
        $totalDeliveryTransfer = $ordersDelivery->where('payment_method', 'transfer')->sum('total');
        $totalPending = $ordersPending->sum('total');
        $totalCash = $totalTable + $totalDeliveryCash;
        $totalGeneral = $totalTable + $ordersDelivery->sum('total');

        return view('tenant.caja.arqueo', compact(
            'ordersTable',
            'ordersDelivery',
            'ordersPending',
            'totalTable',
            'totalDeliveryCash',
            'totalDeliveryTransfer',
            'totalPending',
            'totalCash',
            'totalGeneral'
        ));
    }

    public function cashierShift()
    {
        $userId = session('tenant_user')['id'];

        $openShift = DB::table('cash_registers')
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        $recentShifts = DB::table('cash_registers')
            ->where('user_id', $userId)
            ->where('status', 'closed')
            ->orderBy('closed_at', 'desc')
            ->limit(5)
            ->get();

        return view('tenant.caja.shift', compact('openShift', 'recentShifts'));
    }

    public function cashierOpenShift(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $userId = session('tenant_user')['id'];

        $existingShift = DB::table('cash_registers')
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return redirect()->route('tenant.caja.shift')
                ->with('error', 'Ya tienes un turno abierto.');
        }

        DB::table('cash_registers')->insert([
            'user_id' => $userId,
            'opening_amount' => $request->opening_amount,
            'status' => 'open',
            'opened_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.caja')
            ->with('success', 'Turno abierto correctamente.');
    }

    public function cashierCloseShift(Request $request)
    {
        $request->validate([
            'shift_id' => 'required',
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $userId = session('tenant_user')['id'];
        $shift = DB::table('cash_registers')
            ->where('id', $request->shift_id)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return redirect()->route('tenant.caja.shift')
                ->with('error', 'Turno no encontrado.');
        }

        // Calcular totales del turno
        $shiftStart = $shift->opened_at;

        $totalCash = DB::table('orders')
            ->where('status', 'delivered')
            ->where('payment_method', 'cash')
            ->where('delivered_at', '>=', $shiftStart)
            ->sum('total');

        $totalTransfer = DB::table('orders')
            ->where('status', 'delivered')
            ->where('payment_method', 'transfer')
            ->where('delivered_at', '>=', $shiftStart)
            ->sum('total');

        $totalSales = $totalCash + $totalTransfer;
        $expectedCash = $shift->opening_amount + $totalCash;
        $difference = $request->closing_amount - $expectedCash;

        DB::table('cash_registers')->where('id', $request->shift_id)->update([
            'closing_amount' => $request->closing_amount,
            'total_cash' => $totalCash,
            'total_transfer' => $totalTransfer,
            'total_sales' => $totalSales,
            'difference' => $difference,
            'notes' => $request->notes,
            'status' => 'closed',
            'closed_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.caja.shift.report', $request->shift_id)
            ->with('success', 'Turno cerrado correctamente.');
    }

    public function cashierShiftReport($id)
    {
        $shift = DB::table('cash_registers')
            ->where('id', $id)
            ->first();

        if (!$shift) {
            return redirect()->route('tenant.caja.shift');
        }

        $orders = DB::table('orders')
            ->where('status', 'delivered')
            ->where('delivered_at', '>=', $shift->opened_at)
            ->where('delivered_at', '<=', $shift->closed_at ?? now())
            ->orderBy('delivered_at', 'asc')
            ->get();

        $cancelledOrders = DB::table('orders')
            ->where('status', 'cancelled')
            ->where('updated_at', '>=', $shift->opened_at)
            ->where('updated_at', '<=', $shift->closed_at ?? now())
            ->orderBy('updated_at', 'asc')
            ->get();

        $cashier = DB::table('tenant_users')->where('id', $shift->user_id)->first();
        $tenant = tenancy()->tenant;
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        return view('tenant.caja.shift_report', compact('shift', 'orders', 'cashier', 'tenant', 'settings', 'cancelledOrders'));
    }

    public function cashierCancel($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order || !in_array($order->status, ['pending', 'confirmed', 'dispatched', 'collecting'])) {
            return response()->json(['success' => false, 'message' => 'No se puede cancelar este pedido.']);
        }

        DB::table('orders')->where('id', $id)->update([
            'status' => 'cancelled',
            'updated_at' => now(),
        ]);

        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'order.status', [
            'order_id' => $id,
            'status' => 'cancelled',
            'order_number' => $order->order_number,
        ]);

        return response()->json(['success' => true]);
    }

    public function waiterOrder($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return redirect()->route('tenant.mesero');
        }

        $order->items = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('order_items.*', 'products.name as product_name', 'categories.name as category_name')
            ->where('order_items.order_id', $order->id)
            ->orderBy('categories.name')
            ->get();

        $table = DB::table('tables')->where('id', $order->table_id)->first();

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($categories as $category) {
            $category->products = DB::table('products')
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }

        return view('tenant.mesero.order', compact('order', 'table', 'categories'));
    }

    public function waiterUpdate(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Eliminar items anteriores
        DB::table('order_items')->where('order_id', $id)->delete();

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = DB::table('products')->where('id', $item['product_id'])->first();
            $itemSubtotal = $product->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            DB::table('order_items')->insert([
                'order_id' => $id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $itemSubtotal,
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('orders')->where('id', $id)->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'notes' => $request->notes,
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->where('id', $id)->first();
        $tenantId = $this->getTenantId();

        $this->broadcastEvent('orders.' . $tenantId, 'new.order', [
            'order_id' => $id,
            'order_number' => $order->order_number,
            'type' => 'table',
            'table_id' => $order->table_id,
        ]);

        return redirect()->route('tenant.mesero')
            ->with('success', 'Pedido actualizado correctamente.');
    }

    public function printComanda($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        $order->items = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('order_items.*', 'products.name as product_name', 'categories.name as category_name')
            ->where('order_items.order_id', $order->id)
            ->orderBy('categories.name')
            ->get();

        if ($order->table_id) {
            $order->table = DB::table('tables')->where('id', $order->table_id)->first();
        }

        $tenant = tenancy()->tenant;

        $settings = DB::table('settings')
            ->pluck('value', 'key')
            ->toArray();

        return view('tenant.caja.comanda', compact('order', 'tenant', 'settings'));
    }
}
