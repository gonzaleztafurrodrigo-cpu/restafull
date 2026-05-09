<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function create()
    {
        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('tenant.admin.menu.create_product', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::table('products')->insert([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
            'order' => $request->order ?? 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Producto creado correctamente.');
    }

    public function toggle($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        DB::table('products')->where('id', $id)->update([
            'is_active' => !$product->is_active,
            'updated_at' => now(),
        ]);

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Estado del producto actualizado.');
    }

    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        DB::table('products')->where('id', $id)->delete();

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) return redirect()->route('tenant.admin.menu');

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('tenant.admin.menu.edit_product', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $product = DB::table('products')->where('id', $id)->first();
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        DB::table('products')->where('id', $id)->update($data);

        return redirect()->route('tenant.admin.menu')
            ->with('success', 'Producto actualizado correctamente.');
    }
}
