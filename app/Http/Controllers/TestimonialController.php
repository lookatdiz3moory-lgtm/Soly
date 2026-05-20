<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $testimonials = Testimonial::published()
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->orderBy('sort_order')
            ->get();

        return view()->exists('pages.testimonials')
            ? view('pages.testimonials', compact('testimonials'))
            : view('pages.placeholder', [
                'pageTitle'   => 'Patient Testimonials',
                'pageMessage' => $testimonials->isEmpty()
                    ? 'Patient reviews will appear here soon.'
                    : 'Read ' . $testimonials->count() . ' patient reviews — full layout coming soon.',
            ]);
    }
}
