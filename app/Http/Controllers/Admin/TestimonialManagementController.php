<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TestimonialManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Testimonial::class);

        $items = Testimonial::orderByDesc('is_approved')
            ->orderByDesc('id')
            ->get();

        return view()->exists('admin.testimonials.index')
            ? view('admin.testimonials.index', compact('items'))
            : view('admin.placeholder', [
                'pageTitle' => 'Testimonials',
                'records'   => $items->count() . ' testimonials (' . $items->where('is_approved', false)->count() . ' awaiting approval)',
                'items'     => $items,
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Testimonial::class);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'rating'       => ['required', 'integer', 'min:1', 'max:5'],
            'title'        => ['nullable', 'string', 'max:150'],
            'review'       => ['required', 'string', 'min:5', 'max:2000'],
            'service'      => ['nullable', 'string', 'max:120'],
            'source'       => ['nullable', 'in:internal,google,facebook,whatsapp'],
            'is_featured'  => ['nullable', 'boolean'],
            'is_approved'  => ['nullable', 'boolean'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        Testimonial::create(array_merge($data, [
            'source' => $data['source'] ?? 'internal',
        ]));

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial added.');
    }

    public function approve(int $id): JsonResponse
    {
        $item = Testimonial::findOrFail($id);
        Gate::authorize('update', $item);

        $item->update(['is_approved' => true, 'is_active' => true]);

        return response()->json([
            'success'     => true,
            'is_approved' => true,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = Testimonial::findOrFail($id);
        Gate::authorize('delete', $item);
        $item->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial removed.');
    }
}
