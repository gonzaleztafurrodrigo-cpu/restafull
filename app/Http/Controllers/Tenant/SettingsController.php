<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $bankAccounts = DB::table('bank_accounts')->orderBy('is_active', 'desc')->get();
        return view('tenant.admin.settings.index', compact('settings', 'bankAccounts'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'restaurant_phone' => 'nullable|string|max:20',
            'delivery_time' => 'nullable|string|max:50',
            'min_order' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        $fields = [
            'restaurant_name',
            'restaurant_phone',
            'restaurant_email',
            'restaurant_address',
            'delivery_time',
            'min_order',
            'schedule_mon_fri',
            'schedule_sat',
            'schedule_sun',
            'primary_color',
            'delivery_cost',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $field],
                    ['value' => $request->$field, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        if ($request->hasFile('logo')) {
            $existing = DB::table('settings')->where('key', 'logo')->first();
            if ($existing && $existing->value) {
                Storage::disk('public')->delete($existing->value);
            }
            $path = $request->file('logo')->store('logos', 'public');
            DB::table('settings')->updateOrInsert(
                ['key' => 'logo'],
                ['value' => $path, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return redirect()->route('tenant.admin.settings')
            ->with('success', 'Configuración actualizada correctamente.');
    }

    public function storeBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_type' => 'required|string',
            'account_number' => 'required|string|max:50',
            'owner_name' => 'required|string|max:255',
            'owner_id' => 'nullable|string|max:50',
        ]);

        DB::table('bank_accounts')->insert([
            'bank_name' => $request->bank_name,
            'account_type' => $request->account_type,
            'account_number' => $request->account_number,
            'owner_name' => $request->owner_name,
            'owner_id' => $request->owner_id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.settings')
            ->with('success', 'Cuenta bancaria agregada correctamente.');
    }

    public function destroyBankAccount($id)
    {
        DB::table('bank_accounts')->where('id', $id)->delete();
        return redirect()->route('tenant.admin.settings')
            ->with('success', 'Cuenta bancaria eliminada.');
    }

    public function toggleBankAccount($id)
    {
        $account = DB::table('bank_accounts')->where('id', $id)->first();
        DB::table('bank_accounts')->where('id', $id)->update([
            'is_active' => !$account->is_active,
            'updated_at' => now(),
        ]);
        return redirect()->route('tenant.admin.settings')
            ->with('success', 'Cuenta bancaria actualizada.');
    }
}
