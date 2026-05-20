<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::active()->get();

        return view()->exists('pages.doctors')
            ? view('pages.doctors', compact('doctors'))
            : view('pages.placeholder', [
                'pageTitle'   => 'Our Doctors',
                'pageMessage' => $doctors->isEmpty()
                    ? 'Our team profiles are being prepared.'
                    : 'Meet our team of ' . $doctors->count() . ' qualified specialists. Profiles coming soon.',
            ]);
    }

    public function show(int $id): View
    {
        $doctor = Doctor::with('services:id,name,slug')
            ->where('is_active', true)
            ->findOrFail($id);

        return view()->exists('pages.doctor-show')
            ? view('pages.doctor-show', compact('doctor'))
            : view('pages.placeholder', [
                'pageTitle'   => $doctor->name,
                'pageMessage' => $doctor->bio_excerpt ?? ($doctor->bio ?? 'Doctor profile coming soon.'),
            ]);
    }
}
