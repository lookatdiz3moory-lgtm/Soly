<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::active()->get();

        return view()->exists('pages.services')
            ? view('pages.services', compact('services'))
            : view('pages.placeholder', [
                'pageTitle'   => 'Our Services',
                'pageMessage' => $services->isEmpty()
                    ? 'Our service catalogue is being prepared.'
                    : 'We currently offer ' . $services->count() . ' services. Detailed listings are coming soon.',
            ]);
    }

    public function show(string $slug): View
    {
        $service = Service::with(['doctors' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view()->exists('pages.service-show')
            ? view('pages.service-show', compact('service'))
            : view('pages.placeholder', [
                'pageTitle'   => $service->name,
                'pageMessage' => $service->short_description ?: 'Detailed information about this service is being prepared.',
            ]);
    }
}
