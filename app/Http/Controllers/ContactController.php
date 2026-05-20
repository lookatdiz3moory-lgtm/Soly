<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view()->exists('pages.contact')
            ? view('pages.contact')
            : view('pages.placeholder', [
                'pageTitle'   => 'Contact Us',
                'pageMessage' => 'Reach us at ' . config('clinic.phone', '+20 100 000 0000')
                              . ' or ' . config('clinic.email', 'info@solyclinic.com') . '.',
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['nullable', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:25'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        if (Schema::hasTable('contact_messages')) {
            DB::table('contact_messages')->insert(array_merge($data, [
                'ip_address' => $request->ip(),
                'status'     => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        return redirect()->route('contact.index')
            ->with('success', 'Thank you — we have received your message and will reply soon.');
    }
}
