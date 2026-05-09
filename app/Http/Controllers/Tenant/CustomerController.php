<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function welcome()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $customer = session('customer');
        return view('tenant.client.welcome', compact('settings', 'customer'));
    }

    public function showLogin()
    {
        if (session()->has('customer')) {
            return redirect()->route('tenant.client.dashboard');
        }
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return view('tenant.client.auth.login', compact('settings'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = DB::table('customers')
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.']);
        }

        session(['customer' => [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ]]);

        return redirect()->route('tenant.client.dashboard');
    }

    public function showRegister()
    {
        if (session()->has('customer')) {
            return redirect()->route('tenant.client.dashboard');
        }
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return view('tenant.client.auth.register', compact('settings'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $exists = DB::table('customers')->where('email', $request->email)->exists();
        if ($exists) {
            return back()->withErrors(['email' => 'Este correo ya está registrado.'])->withInput();
        }

        $id = DB::table('customers')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session(['customer' => [
            'id' => $id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]]);

        return redirect()->route('tenant.client.dashboard');
    }

    public function logout()
    {
        session()->forget('customer');
        return redirect()->route('tenant.welcome');
    }

    public function dashboard()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $customer = session('customer');

        $recentOrders = DB::table('orders')
            ->where('customer_id', $customer['id'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $defaultAddress = DB::table('customer_addresses')
            ->where('customer_id', $customer['id'])
            ->where('is_default', true)
            ->first();

        return view('tenant.client.dashboard', compact('settings', 'customer', 'recentOrders', 'defaultAddress'));
    }

    public function orders()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $customer = session('customer');

        $orders = DB::table('orders')
            ->where('customer_id', $customer['id'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($orders as $order) {
            $order->items = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('order_items.*', 'products.name as product_name')
                ->where('order_items.order_id', $order->id)
                ->get();
        }

        return view('tenant.client.orders', compact('settings', 'customer', 'orders'));
    }

    public function addresses()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $customer = session('customer');

        $addresses = DB::table('customer_addresses')
            ->where('customer_id', $customer['id'])
            ->orderBy('is_default', 'desc')
            ->get();

        return view('tenant.client.addresses', compact('settings', 'customer', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'address' => 'required|string',
        ]);

        $customer = session('customer');

        $isDefault = DB::table('customer_addresses')
            ->where('customer_id', $customer['id'])
            ->count() === 0;

        if ($request->is_default) {
            DB::table('customer_addresses')
                ->where('customer_id', $customer['id'])
                ->update(['is_default' => false]);
            $isDefault = true;
        }

        DB::table('customer_addresses')->insert([
            'customer_id' => $customer['id'],
            'label' => $request->label,
            'address' => $request->address,
            'is_default' => $isDefault,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.client.addresses')
            ->with('success', 'Dirección guardada correctamente.');
    }

    public function destroyAddress($id)
    {
        $customer = session('customer');
        DB::table('customer_addresses')
            ->where('id', $id)
            ->where('customer_id', $customer['id'])
            ->delete();

        return redirect()->route('tenant.client.addresses')
            ->with('success', 'Dirección eliminada.');
    }

    public function setDefaultAddress($id)
    {
        $customer = session('customer');

        DB::table('customer_addresses')
            ->where('customer_id', $customer['id'])
            ->update(['is_default' => false]);

        DB::table('customer_addresses')
            ->where('id', $id)
            ->where('customer_id', $customer['id'])
            ->update(['is_default' => true]);

        return redirect()->route('tenant.client.addresses')
            ->with('success', 'Dirección predeterminada actualizada.');
    }
}
