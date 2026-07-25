<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CatalogType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::query()
            ->with('category')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('name_ar', 'like', $term)->orWhere('name_en', 'like', $term));
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.services.index', ['services' => $services]);
    }

    public function create(): View
    {
        return view('admin.services.form', [
            'service' => new Service(['is_active' => true, 'is_bookable' => true]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $request->payload();
        $data['image_path'] = $this->storeImage($request);

        Service::create(array_filter($data, fn ($v) => $v !== null));

        return redirect()->route('admin.services.index')->with('status', __('admin.saved'));
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', [
            'service' => $service,
            'categories' => $this->categories(),
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->payload();

        if ($path = $this->storeImage($request)) {
            $this->deleteImage($service->image_path);
            $data['image_path'] = $path;
        }

        $service->update($data);

        return redirect()->route('admin.services.index')->with('status', __('admin.saved'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete(); // soft delete: past orders keep their reference

        return back()->with('status', __('admin.deleted'));
    }

    private function categories()
    {
        return Category::ofType(CatalogType::Service)->ordered()->get();
    }

    private function storeImage(ServiceRequest $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('services', 'public')
            : null;
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
