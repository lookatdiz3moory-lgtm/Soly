<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * DashboardController
 *
 * Provides the main admin dashboard view and a live-refresh stats endpoint.
 *
 * Routes:
 *   GET /admin/dashboard        → index()
 *   GET /admin/dashboard/stats  → stats()  (JSON, called by JS every 60s)
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'stats'          => $this->buildStats(),
            'todaySchedule'  => $this->todaySchedule(),
            'recentActivity' => $this->recentActivity(),
            'weeklyChart'    => $this->weeklyBookings(),
        ]);
    }

    /**
     * Return fresh stats as JSON — polled by the dashboard JS.
     */
    public function stats(): JsonResponse
    {
        return response()->json($this->buildStats());
    }

    /* ── Private builders ───────────────────────────────────── */

    private function buildStats(): array
    {
        $today = today()->toDateString();
        $month = today()->format('Y-m');

        return [
            // Volume
            'total_appointments' => Appointment::count(),
            'today_count'        => Appointment::today()->count(),
            'month_count'        => Appointment::whereRaw("DATE_FORMAT(appointment_date,'%Y-%m') = ?", [$month])->count(),

            // Status breakdown
            'pending'    => Appointment::pending()->count(),
            'confirmed'  => Appointment::confirmed()->count(),
            'completed'  => Appointment::completed()->count(),
            'cancelled'  => Appointment::where('status', Appointment::STATUS_CANCELLED)->count(),
            'no_show'    => Appointment::where('status', Appointment::STATUS_NO_SHOW)->count(),

            // Upcoming today
            'today_upcoming' => Appointment::today()
                ->whereIn('status', Appointment::ACTIVE_STATUSES)
                ->count(),

            // Patients
            'total_patients' => User::patients()->count(),
            'new_patients_month' => User::patients()
                ->whereRaw("DATE_FORMAT(created_at,'%Y-%m') = ?", [$month])
                ->count(),

            // Revenue (completed appointments with price_paid set)
            'revenue_total' => (float) Appointment::completed()
                ->whereNotNull('price_paid')
                ->sum('price_paid'),
            'revenue_month' => (float) Appointment::completed()
                ->whereNotNull('price_paid')
                ->whereRaw("DATE_FORMAT(appointment_date,'%Y-%m') = ?", [$month])
                ->sum('price_paid'),

            // Clinic
            'active_doctors'   => Doctor::active()->count(),
            'active_services'  => Service::active()->count(),
            'pending_reviews'  => Testimonial::pendingApproval()->count(),
        ];
    }

    private function todaySchedule(): \Illuminate\Support\Collection
    {
        return Appointment::with(['doctor:id,name', 'service:id,name'])
            ->today()
            ->whereIn('status', Appointment::ACTIVE_STATUSES)
            ->orderBy('slot_start')
            ->limit(10)
            ->get();
    }

    private function recentActivity(): \Illuminate\Support\Collection
    {
        return Appointment::with(['service:id,name'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'reference', 'patient_name', 'patient_phone', 'status',
                   'appointment_date', 'slot_start', 'service_id', 'created_at']);
    }

    private function weeklyBookings(): array
    {
        // Last 7 days, grouped by date
        $rows = DB::select("
            SELECT
                DATE(created_at) as day,
                COUNT(*) as count
            FROM appointments
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ");

        // Build a full 7-day array, filling gaps with 0
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i)->toDateString();
            $result[] = [
                'date'  => $date,
                'label' => today()->subDays($i)->format('D'),
                'count' => 0,
            ];
        }

        foreach ($rows as $row) {
            foreach ($result as &$slot) {
                if ($slot['date'] === $row->day) {
                    $slot['count'] = (int) $row->count;
                }
            }
        }

        return $result;
    }
}
