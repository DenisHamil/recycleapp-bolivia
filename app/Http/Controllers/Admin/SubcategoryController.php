<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100'
        ]);

        Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name
        ]);

        return redirect()->route('admin.categories')->with('success', 'Subcategoría añadida correctamente.');
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return redirect()->route('admin.categories')->with('success', 'Subcategoría eliminada.');
    }
}
