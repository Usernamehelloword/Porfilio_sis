<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash', 'unique:categories,slug'],
        ]);

        $data['slug'] ??= str($data['name'])->slug()->toString();

        Category::create($data + ['display_order' => (int) Category::max('display_order') + 1]);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $category->update($data + ['is_published' => $request->boolean('is_published')]);

        return back()->with('success', 'Category updated.');
    }

    public function togglePublish(Category $category): RedirectResponse
    {
        $category->is_published = ! $category->is_published;
        $category->save();

        return back()->with('success', $category->is_published ? 'Category published.' : 'Category hidden.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->projects()->exists()) {
            return back()->withErrors(['category' => 'This category still has projects assigned to it.']);
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
