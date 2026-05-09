<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($categories as $category) {
            $category->products_count = DB::table('products')
                ->where('category_id', $category->id)
                ->count();
        }

        return view('tenant.admin.menu.index', compact('categories'));
    }

    public function create()
    {
        return view('tenant.admin.menu.create_category');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        DB::table('categories')->insert([
            'name' => $request->name,
            'order' => $request->order ?? 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function toggle($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        DB::table('categories')->where('id', $id)->update([
            'is_active' => !$category->is_active,
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Estado de la categoría actualizado.');
    }

    public function destroy($id)
    {
        DB::table('products')->where('category_id', $id)->delete();
        DB::table('categories')->where('id', $id)->delete();

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Categoría eliminada correctamente.');
    }

    public function edit($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        if (!$category) return redirect()->route('tenant.admin.menu');
        return view('tenant.admin.menu.edit_category', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        DB::table('categories')->where('id', $id)->update([
            'name' => $request->name,
            'order' => $request->order ?? 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Categoría actualizada correctamente.');
    }
}
