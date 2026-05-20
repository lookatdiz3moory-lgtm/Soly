<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DoctorManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Doctor::class);
        $doctors = Doctor::orderBy('sort_order')->orderBy('name')->get();
        return $this->render('admin.doctors.index', 'Doctors', compact('doctors'), $doctors->count() . ' doctors');
    }

    public function create(): View
    {
        Gate::authorize('create', Doctor::class);
        return $this->render('admin.doctors.create', 'Add Doctor', [
            'services' => Service::active()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Doctor::class);
        $doctor = Doctor::create($this->validateDoctor($request));
        if ($request->filled('services')) {
            $doctor->services()->sync((array) $request->input('services'));
        }
        return redirect()->route('admin.doctors.index')
            ->with('success', "Doctor {$doctor->name} created.");
    }

    public function edit(int $id): View
    {
        $doctor = Doctor::findOrFail($id);
        Gate::authorize('update', $doctor);
        return $this->render('admin.doctors.edit', 'Edit Doctor', [
            'doctor'   => $doctor,
            'services' => Service::active()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $doctor = Doctor::findOrFail($id);
        Gate::authorize('update', $doctor);
        $doctor->update($this->validateDoctor($request));
        if ($request->has('services')) {
            $doctor->services()->sync((array) $request->input('services'));
        }
        return redirect()->route('admin.doctors.index')
            ->with('success', "Doctor {$doctor->name} updated.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $doctor = Doctor::findOrFail($id);
        Gate::authorize('delete', $doctor);
        $doctor->delete();
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor removed.');
    }

    public function schedule(int $id): View
    {
        $doctor = Doctor::findOrFail($id);
        Gate::authorize('update', $doctor);
        return $this->render('admin.doctors.schedule', 'Doctor Schedule', compact('doctor'));
    }

    public function updateSchedule(Request $request, int $id): RedirectResponse
    {
        $doctor = Doctor::findOrFail($id);
        Gate::authorize('update', $doctor);

        $schedule = $request->input('schedule', []);
        if (is_string($schedule)) {
            $decoded = json_decode($schedule, true);
            $schedule = is_array($decoded) ? $decoded : [];
        }

        $doctor->update(['schedule' => $schedule]);

        return redirect()->route('admin.doctors.schedule', $doctor->id)
            ->with('success', 'Schedule updated.');
    }

    /* ── Helpers ────────────────────────────────────────────── */

    private function validateDoctor(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:120'],
            'title'          => ['nullable', 'string', 'max:80'],
            'specialty'      => ['nullable', 'string', 'max:120'],
            'bio'            => ['nullable', 'string', 'max:5000'],
            'phone'          => ['nullable', 'string', 'max:25'],
            'email'          => ['nullable', 'email', 'max:150'],
            'experience'     => ['nullable', 'string', 'max:80'],
            'slot_duration'  => ['nullable', 'integer', 'min:5', 'max:240'],
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
