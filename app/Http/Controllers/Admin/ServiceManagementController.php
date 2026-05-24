<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Service::class);
        $services = Service::orderBy('sort_order')->orderBy('name')->get();
        return $this->render('admin.services.index', 'Services', compact('services'), $services->count() . ' services');
    }

    public function create(): View
    {
        Gate::authorize('create', Service::class);
        return $this->render('admin.services.create', 'Add Service');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Service::class);
        $data = $this->validateService($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        $service = Service::create($data);
        return redirect()->route('admin.services.index')
            ->with('success', "Service {$service->name} created.");
    }

    public function edit(int $id): View
    {
        $service = Service::findOrFail($id);
        Gate::authorize('update', $service);
        return $this->render('admin.services.edit', 'Edit Service', compact('service'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        Gate::authorize('update', $service);
        $data = $this->validateService($request);
        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        $service->update($data);
        return redirect()->route('admin.services.index')
            ->with('success', "Service {$service->name} updated.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $service = Service::findOrFail($id);
        Gate::authorize('delete', $service);
        $this->deleteUploadedImage($service->image);
        $service->delete();
        return redirect()->route('admin.services.index')
            ->with('success', 'Service removed.');
    }

    private function validateService(Request $request): array
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:120'],
            'name_ar'           => ['nullable', 'string', 'max:120'],
            'slug'              => ['nullable', 'string', 'max:140'],
            'category'          => ['nullable', 'string', 'max:80'],
            'short_description'    => ['nullable', 'string', 'max:300'],
            'short_description_ar' => ['nullable', 'string', 'max:300'],
            'description'          => ['nullable', 'string', 'max:8000'],
            'description_ar'       => ['nullable', 'string', 'max:8000'],
            'price_from'        => ['nullable', 'numeric', 'min:0'],
            'price_to'          => ['nullable', 'numeric', 'min:0'],
            'duration_minutes'  => ['nullable', 'integer', 'min:0', 'max:1440'],
            'is_featured'       => ['nullable', 'boolean'],
            'is_bookable'       => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // The image file is handled separately in store/update.
        unset($data['image']);

        return $data;
    }

    /**
     * Delete a previously uploaded image from the public disk.
     * Skips seeded images stored under public/images/ (path prefix "images/").
     */
    private function deleteUploadedImage(?string $path): void
    {
        if (!$path || str_starts_with($path, 'images/')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }

    private function render(string $view, string $title, array $data = [], ?string $records = null): View
    {
        return view()->exists($view)
            ? view($view, $data)
            : view('admin.placeholder', array_merge([
                'pageTitle' => $title,
                'records'   => $records,
            ], $data));
    }
}
