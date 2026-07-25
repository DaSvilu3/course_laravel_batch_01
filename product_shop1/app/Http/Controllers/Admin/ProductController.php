<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CatalogType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('name_ar', 'like', $term)->orWhere('name_en', 'like', $term));
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', ['products' => $products]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->payload();
        $data['image_path'] = $this->storeImage($request);

        Product::create(array_filter($data, fn ($v) => $v !== null));

        return redirect()->route('admin.products.index')->with('status', __('admin.saved'));
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => $this->categories(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->payload();

        if ($path = $this->storeImage($request)) {
            $this->deleteImage($product->image_path);
            $data['image_path'] = $path;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('status', __('admin.saved'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('status', __('admin.deleted'));
    }

    private function categories()
    {
        return Category::ofType(CatalogType::Product)->ordered()->get();
    }

    private function storeImage(ProductRequest $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
