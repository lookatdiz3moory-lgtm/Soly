<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DoctorManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Doctor::class);
        $doctors = Doctor::with('services:id,name')->orderBy('sort_order')->orderBy('name')->get();
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
        $data = $this->validateDoctor($request);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }
        $doctor = Doctor::create($data);
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
        $data = $this->validateDoctor($request);
        if ($request->hasFile('photo')) {
            $this->deleteUploadedImage($doctor->photo);
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }
        $doctor->update($data);
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

        if ($doctor->appointments()->exists()) {
            return redirect()->route('admin.doctors.index')
                ->with('error', "Cannot delete Dr. {$doctor->name} — they have linked appointments. Reassign or cancel those appointments first.");
        }

        $this->deleteUploadedImage($doctor->photo);
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

        // Form submits per-day flat inputs (monday_active, monday_open, monday_close, …)
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $schedule = [];
        foreach ($days as $day) {
            $schedule[$day] = [
                'active' => $request->boolean("{$day}_active"),
                'open'   => $request->input("{$day}_open", '09:00'),
                'close'  => $request->input("{$day}_close", '21:00'),
            ];
        }

        $doctor->update(['schedule' => $schedule]);

        return redirect()->route('admin.doctors.schedule', $doctor->id)
            ->with('success', 'Schedule updated.');
    }

    /* ── Helpers ────────────────────────────────────────────── */

    private function validateDoctor(Request $request): array
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:120'],
            'name_ar'        => ['nullable', 'string', 'max:120'],
            'title'          => ['nullable', 'string', 'max:80'],
            'specialty'      => ['nullable', 'string', 'max:120'],
            'specialty_ar'   => ['nullable', 'string', 'max:120'],
            'bio'            => ['nullable', 'string', 'max:5000'],
            'bio_ar'         => ['nullable', 'string', 'max:5000'],
            'phone'          => ['nullable', 'string', 'max:25'],
            'email'          => ['nullable', 'email', 'max:150'],
            'experience'     => ['nullable', 'string', 'max:80'],
            'slot_duration'  => ['nullable', 'integer', 'min:5', 'max:240'],
            'is_active'      => ['nullable', 'boolean'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
            'specialties'    => ['nullable', 'array'],
            'specialties.*'  => ['string', 'max:100'],
            'languages'      => ['nullable', 'array'],
            'languages.*'    => ['string', 'max:80'],
            'photo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // The photo file is handled separately in store/update.
        unset($data['photo']);

        // Convert comma-separated textarea inputs to arrays if submitted as strings
        foreach (['specialties', 'languages'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = array_filter(array_map('trim', explode(',', $data[$field])));
            }
        }

        return $data;
    }

    /**
     * Delete a previously uploaded photo from the public disk.
     * Skips seeded images stored under public/images/ (path prefix "images/").
     */
    private function deleteUploadedImage(?string $path): void
    {
        if (!$path || str_starts_with($path, 'images/')) {
            return;
        }
        Storage::disk('public')->delete($path);
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
