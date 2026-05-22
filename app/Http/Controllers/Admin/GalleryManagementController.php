<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GalleryManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Gallery::class);
        $items = Gallery::with('service:id,name')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view()->exists('admin.gallery.index')
            ? view('admin.gallery.index', compact('items'))
            : view('admin.placeholder', [
                'pageTitle' => 'Gallery',
                'records'   => $items->count() . ' items',
                'items'     => $items,
            ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        Gate::authorize('create', Gallery::class);

        $data = $request->validate([
            'service_id'   => ['nullable', 'integer', 'exists:services,id'],
            'type'         => ['required', 'in:before_after,general,clinic,team'],
            'title'        => ['nullable', 'string', 'max:200'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'treatment'    => ['nullable', 'string', 'max:120'],
            'image'        => ['nullable', 'image', 'max:5120'],
            'before_image' => ['nullable', 'image', 'max:5120'],
            'after_image'  => ['nullable', 'image', 'max:5120'],
            'is_featured'  => ['nullable', 'boolean'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        foreach (['image', 'before_image', 'after_image'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('gallery', 'public');
            }
        }

        Gallery::create($data);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item uploaded.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = Gallery::findOrFail($id);
        Gate::authorize('delete', $item);
        $item->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item removed.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $item = Gallery::findOrFail($id);
        Gate::authorize('update', $item);

        $item->update(['is_active' => !$item->is_active]);

        return redirect()->route('admin.gallery.index')
            ->with('success', $item->is_active ? 'Item published.' : 'Item hidden.');
    }
}
