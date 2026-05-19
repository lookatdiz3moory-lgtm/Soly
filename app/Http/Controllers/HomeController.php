<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Gallery;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the clinic homepage.
     *
     * Loads featured services, active doctors, approved testimonials,
     * before/after gallery items, and featured FAQs.
     * All queries degrade gracefully when tables are empty.
     */
    public function index(): View
    {
        // ── Featured Services (up to 6) ──────────────────────────
        $featuredServices = $this->loadFeaturedServices();

        // ── Active Doctors ───────────────────────────────────────
        $doctors = $this->loadDoctors();

        // ── Approved Testimonials (up to 6) ──────────────────────
        $testimonials = $this->loadTestimonials();

        // ── Before/After Gallery (up to 4) ───────────────────────
        $beforeAfterItems = $this->loadBeforeAfter();

        // ── FAQ (featured only, up to 6) ─────────────────────────
        $faqs = $this->loadFaqs();

        return view('pages.home', compact(
            'featuredServices',
            'doctors',
            'testimonials',
            'beforeAfterItems',
            'faqs'
        ));
    }

    /* ─── Private Loaders ──────────────────────────────────────── */

    private function loadFeaturedServices(): \Illuminate\Support\Collection
    {
        try {
            return Service::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(6)
                ->get();
        } catch (\Exception) {
            // Table may not exist yet (before migrations)
            return collect();
        }
    }

    private function loadDoctors(): \Illuminate\Support\Collection
    {
        try {
            return Doctor::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(3)
                ->get();
        } catch (\Exception) {
            return collect();
        }
    }

    private function loadTestimonials(): \Illuminate\Support\Collection
    {
        try {
            return Testimonial::query()
                ->where('is_active', true)
                ->where('is_approved', true)
                ->orderByDesc('is_featured')
                ->orderByDesc('rating')
                ->take(6)
                ->get();
        } catch (\Exception) {
            return collect();
        }
    }

    private function loadBeforeAfter(): \Illuminate\Support\Collection
    {
        try {
            return Gallery::query()
                ->where('type', 'before_after')
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->take(4)
                ->get();
        } catch (\Exception) {
            return collect();
        }
    }

    private function loadFaqs(): \Illuminate\Support\Collection
    {
        try {
            // Requires App\Models\Faq — created in Phase 2
            if (!class_exists(\App\Models\Faq::class)) {
                return collect();
            }
            return \App\Models\Faq::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->take(6)
                ->get();
        } catch (\Exception) {
            return collect();
        }
    }
}
