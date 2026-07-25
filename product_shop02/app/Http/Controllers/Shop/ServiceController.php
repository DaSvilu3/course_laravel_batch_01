<?php

namespace App\Http\Controllers\Shop;

use App\Enums\CatalogType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::query()
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

        return view('shop.services.index', [
            'services' => $services,
            'categories' => Category::active()->ofType(CatalogType::Service)->ordered()->get(),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);

        return view('shop.services.show', [
            'service' => $service->load('category'),
            'related' => Service::active()
                ->where('category_id', $service->category_id)
                ->whereKeyNot($service->id)
                ->ordered()
                ->take(3)
                ->get(),
        ]);
    }
}
