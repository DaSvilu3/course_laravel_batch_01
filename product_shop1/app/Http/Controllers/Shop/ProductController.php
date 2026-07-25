<?php

namespace App\Http\Controllers\Shop;

use App\Enums\CatalogType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->active()
            ->with('category')
            ->when($request->filled('category'), fn ($q) => $q->whereHas(
                'category',
                fn ($c) => $c->where('slug', $request->string('category'))
            ))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('name_ar', 'like', $term)->orWhere('name_en', 'like', $term));
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('shop.products.index', [
            'products' => $products,
            'categories' => Category::active()->ofType(CatalogType::Product)->ordered()->get(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('shop.products.show', [
            'product' => $product->load('category'),
            'related' => Product::active()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->ordered()
                ->take(3)
                ->get(),
        ]);
    }
}
