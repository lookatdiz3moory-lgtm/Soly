<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $items = Gallery::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view()->exists('pages.gallery')
            ? view('pages.gallery', compact('items'))
            : view('pages.placeholder', [
                'pageTitle'   => 'Gallery',
                'pageMessage' => $items->isEmpty()
                    ? 'Our before/after gallery is being prepared.'
                    : 'View ' . $items->count() . ' gallery items — full layout coming soon.',
            ]);
    }
}
