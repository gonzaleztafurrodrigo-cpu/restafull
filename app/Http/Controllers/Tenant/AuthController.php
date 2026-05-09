<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('tenant_user')) {
            return $this->redirectByRole(session('tenant_user')['role']);
        }
        return view('tenant.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = TenantUser::where('email', $request->email)
            ->where('is_active', true)
            ->with('role')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Credenciales incorrectas.',
            ]);
        }

        session([
            'tenant_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
                'role_display' => $user->role->display_name,
            ]
        ]);

        return $this->redirectByRole($user->role->name);
    }

    public function logout()
    {
        session()->forget('tenant_user');
        return redirect()->route('tenant.login');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin' => redirect()->route('tenant.admin'),
            'cashier' => redirect()->route('tenant.caja'),
            'waiter' => redirect()->route('tenant.mesero'),
            'delivery' => redirect()->route('tenant.domiciliario'),
            default => redirect()->route('tenant.login'),
        };
    }
}
