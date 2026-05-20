<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::active()->orderBy('sort_order')->orderBy('id')->get();

        return view()->exists('pages.faq')
            ? view('pages.faq', compact('faqs'))
            : view('pages.placeholder', [
                'pageTitle'   => 'Frequently Asked Questions',
                'pageMessage' => $faqs->isEmpty()
                    ? 'Our FAQ section is being prepared.'
                    : 'Browse ' . $faqs->count() . ' frequently asked questions — full layout coming soon.',
            ]);
    }
}
