<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CatalogType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->when($request->filled('type'), fn ($q) => $q->ofType($request->string('type')))
            ->withCount('services', 'products')
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(['type' => CatalogType::Service, 'is_active' => true]),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->payload());

        return redirect()->route('admin.categories.index')->with('status', __('admin.saved'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->payload());

        return redirect()->route('admin.categories.index')->with('status', __('admin.saved'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('status', __('admin.deleted'));
    }
}
