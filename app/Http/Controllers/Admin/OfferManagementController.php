<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OfferManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Offer::class);
        $offers = Offer::with('service:id,name')->orderBy('sort_order')->orderByDesc('id')->get();
        return $this->render('admin.offers.index', 'Offers', compact('offers'), $offers->count() . ' offers');
    }

    public function create(): View
    {
        Gate::authorize('create', Offer::class);
        return $this->render('admin.offers.create', 'Add Offer', [
            'services' => Service::active()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Offer::class);
        $offer = Offer::create($this->validateOffer($request));
        return redirect()->route('admin.offers.index')
            ->with('success', "Offer {$offer->title} created.");
    }

    public function edit(int $id): View
    {
        $offer = Offer::findOrFail($id);
        Gate::authorize('update', $offer);
        return $this->render('admin.offers.edit', 'Edit Offer', [
            'offer'    => $offer,
            'services' => Service::active()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $offer = Offer::findOrFail($id);
        Gate::authorize('update', $offer);
        $offer->update($this->validateOffer($request));
        return redirect()->route('admin.offers.index')
            ->with('success', "Offer {$offer->title} updated.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $offer = Offer::findOrFail($id);
        Gate::authorize('delete', $offer);
        $offer->delete();
        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer removed.');
    }

    private function validateOffer(Request $request): array
    {
        return $request->validate([
            'service_id'     => ['nullable', 'integer', 'exists:services,id'],
            'title'          => ['required', 'string', 'max:140'],
            'title_ar'       => ['nullable', 'string', 'max:140'],
            'description'    => ['nullable', 'string', 'max:5000'],
            'original_price' => ['required', 'numeric', 'min:0'],
            'offer_price'    => ['required', 'numeric', 'min:0', 'lte:original_price'],
            'badge_text'     => ['nullable', 'string', 'max:50'],
            'terms'          => ['nullable', 'string', 'max:2000'],
            'starts_at'      => ['nullable', 'date'],
            'expires_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_featured'    => ['nullable', 'boolean'],
            'is_active'      => ['nullable', 'boolean'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);
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
