<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    public function index()
    {
        $tables = DB::table('tables')
            ->orderBy('name')
            ->get();

        return view('tenant.admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tenant.admin.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $exists = DB::table('tables')->where('name', $request->name)->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ya existe una mesa con ese nombre.'])->withInput();
        }

        DB::table('tables')->insert([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'status' => 'available',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.tables')
            ->with('success', 'Mesa creada correctamente.');
    }

    public function toggle($id)
    {
        $table = DB::table('tables')->where('id', $id)->first();

        DB::table('tables')->where('id', $id)->update([
            'is_active' => !$table->is_active,
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.tables')
            ->with('success', 'Estado de la mesa actualizado.');
    }

    public function destroy($id)
    {
        DB::table('tables')->where('id', $id)->delete();

        return redirect()->route('tenant.admin.tables')
            ->with('success', 'Mesa eliminada correctamente.');
    }

    public function edit($id)
    {
        $table = DB::table('tables')->where('id', $id)->first();
        if (!$table) return redirect()->route('tenant.admin.tables');
        return view('tenant.admin.tables.edit', compact('table'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $exists = DB::table('tables')
            ->where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ya existe una mesa con ese nombre.'])->withInput();
        }

        DB::table('tables')->where('id', $id)->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.tables')
            ->with('success', 'Mesa actualizada correctamente.');
    }
}
