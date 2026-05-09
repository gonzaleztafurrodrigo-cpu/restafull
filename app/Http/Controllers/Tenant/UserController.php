<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = DB::table('tenant_users')
            ->join('roles', 'tenant_users.role_id', '=', 'roles.id')
            ->select('tenant_users.*', 'roles.display_name as role_name', 'roles.name as role_slug')
            ->whereIn('roles.name', ['admin', 'cashier', 'waiter', 'delivery'])
            ->orderBy('tenant_users.created_at', 'desc')
            ->get();

        return view('tenant.admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = DB::table('roles')
            ->whereIn('name', ['admin', 'cashier', 'waiter', 'delivery'])
            ->get();

        return view('tenant.admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role_id' => 'required',
            'phone' => 'nullable|string|max:20',
        ]);

        $exists = DB::table('tenant_users')->where('email', $request->email)->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Este email ya está registrado.'])->withInput();
        }

        DB::table('tenant_users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.users')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function toggle($id)
    {
        $user = DB::table('tenant_users')->where('id', $id)->first();

        DB::table('tenant_users')->where('id', $id)->update([
            'is_active' => !$user->is_active,
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.users')
            ->with('success', 'Estado del usuario actualizado.');
    }

    public function destroy($id)
    {
        DB::table('tenant_users')->where('id', $id)->delete();

        return redirect()->route('tenant.admin.users')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function edit($id)
    {
        $user = DB::table('tenant_users')->where('id', $id)->first();
        if (!$user) return redirect()->route('tenant.admin.users');

        $roles = DB::table('roles')
            ->whereIn('name', ['admin', 'cashier', 'waiter', 'delivery'])
            ->get();

        return view('tenant.admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
        ]);

        $exists = DB::table('tenant_users')
            ->where('email', $request->email)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'Este email ya está registrado.'])->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        DB::table('tenant_users')->where('id', $id)->update($data);

        return redirect()->route('tenant.admin.users')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}
