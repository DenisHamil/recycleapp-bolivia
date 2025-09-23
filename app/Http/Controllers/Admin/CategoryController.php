<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->orderBy('name')->get();
        return view('admin.modules.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'points_per_kilo' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:20', // ✅ validación de color opcional
        ]);

        Category::create($request->only('name', 'points_per_kilo', 'color')); // ✅ incluye color

        return redirect()->route('admin.categories')->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'points_per_kilo' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:20', // ✅ validación de color opcional
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only('name', 'points_per_kilo', 'color')); // ✅ incluye color

        return redirect()->route('admin.categories')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        Category::destroy($id);

        return redirect()->route('admin.categories')->with('success', 'Categoría eliminada.');
    }
}
