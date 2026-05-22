<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = Offer::with('service:id,name,slug')->active()->orderByDesc('is_featured')->orderBy('sort_order')->get();

        return view()->exists('pages.offers')
            ? view('pages.offers', compact('offers'))
            : view('pages.placeholder', [
                'pageTitle'   => 'Special Offers',
                'pageMessage' => $offers->isEmpty()
                    ? 'No active offers right now — please check back soon.'
                    : 'We currently have ' . $offers->count() . ' active offers — detailed pages coming soon.',
            ]);
    }

    public function show(string $slug): View
    {
        $offer = Offer::with('service:id,name,slug')
            ->where('is_active', true)
            ->where(function ($q) use ($slug): void {
                $q->whereRaw('LOWER(title) = ?', [strtolower(str_replace('-', ' ', $slug))])
                  ->orWhere('id', is_numeric($slug) ? (int)$slug : 0);
            })
            ->firstOrFail();

        return view()->exists('pages.offer-show')
            ? view('pages.offer-show', compact('offer'))
            : view('pages.placeholder', [
                'pageTitle'   => $offer->title,
                'pageMessage' => $offer->description ?: 'Offer details coming soon.',
            ]);
    }
}
