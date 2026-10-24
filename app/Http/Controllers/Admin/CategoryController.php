<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.kategori.index', ['categories' => Category::withCount('books')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:categories,name', 'description' => 'nullable|string']);
        $cat = Category::create($data);
        ActivityLog::record('category.created', 'category', $cat->id);

        return back()->with('ok', 'Kategori ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.kategori.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => 'nullable|string',
        ]);
        $category->update($data);
        ActivityLog::record('category.updated', 'category', $category->id);

        return redirect()->route('admin.kategori.index')->with('ok', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->withTrashed()->exists()) {
            return back()->with('err', 'Kategori masih memiliki buku, tidak dapat dihapus.');
        }
        ActivityLog::record('category.deleted', 'category', $category->id);
        $category->delete();

        return back()->with('ok', 'Kategori dihapus.');
    }
}
