<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Doctor Model
 *
 * Represents a clinician. Linked to Services via a many-to-many pivot
 * (doctor_service) and to Appointments via a one-to-many relation.
 *
 * Schedule JSON shape (stored in `schedule` column):
 * {
 *   "monday":    { "open": "09:00", "close": "21:00", "active": true },
 *   "tuesday":   { "open": "09:00", "close": "21:00", "active": true },
 *   ...
 *   "friday":    { "open": "09:00", "close": "21:00", "active": false },
 *   "saturday":  { "open": "10:00", "close": "18:00", "active": true },
 *   "sunday":    { "open": "09:00", "close": "21:00", "active": true }
 * }
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $title           — "BDS, MSc"
 * @property string|null $specialty
 * @property string|null $bio
 * @property string|null $photo           — storage path
 * @property string|null $phone
 * @property string|null $email
 * @property array|null  $qualifications
 * @property array|null  $languages
 * @property array|null  $schedule
 * @property int         $slot_duration   — minutes per slot
 * @property string|null $experience      — "10+ Years"
 * @property array|null  $specialties     — ["Implants","Veneers"]
 * @property bool        $is_active
 * @property int         $sort_order
 */
class Doctor extends Model
{
    protected $fillable = [
        'name',
        'title',
        'specialty',
        'bio',
        'photo',
        'phone',
        'email',
        'qualifications',
        'languages',
        'schedule',
        'slot_duration',
        'experience',
        'specialties',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'qualifications' => 'array',
        'languages'      => 'array',
        'schedule'       => 'array',
        'specialties'    => 'array',
        'is_active'      => 'boolean',
        'slot_duration'  => 'integer',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    /**
     * Services this doctor performs.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * All appointments assigned to this doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Manually blocked time slots (holidays, breaks, etc.).
     */
    public function blockedSlots(): HasMany
    {
        return $this->hasMany(BlockedSlot::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    /**
     * Active doctors ordered by sort_order then name.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->orderBy('sort_order')
                     ->orderBy('name');
    }

    /**
     * Doctors who offer a specific service.
     */
    public function scopeForService(Builder $query, int $serviceId): Builder
    {
        return $query->active()
                     ->whereHas('services', fn(Builder $q) => $q->where('services.id', $serviceId));
    }

    /* ── Accessors ──────────────────────────────────────────── */

    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo) {
            return asset('images/doctor-placeholder.svg');
        }
        if (str_starts_with($this->photo, 'images/')) {
            return asset($this->photo);
        }
        return asset('storage/' . $this->photo);
    }

    /**
     * Short bio safe for card previews (150 chars).
     */
    public function getBioExcerptAttribute(): string
    {
        return \Str::limit($this->bio ?? '', 150);
    }

    /* ── Slot Generation ────────────────────────────────────── */

    /**
     * Generate all theoretical time slots for a given date.
     *
     * Returns array of ['start' => 'HH:MM', 'end' => 'HH:MM']
     * Does NOT subtract booked slots — call availableSlots() for that.
     *
     * @return array<int, array{start: string, end: string}>
     */
    public function allSlots(string $date): array
    {
        $schedule  = $this->schedule ?? [];
        $dayOfWeek = strtolower(date('l', strtotime($date)));
        $day       = $schedule[$dayOfWeek] ?? null;

        if (!$day || empty($day['active'])) {
            return [];
        }

        $duration  = (int)($this->slot_duration ?? 30);
        $openTime  = strtotime("{$date} {$day['open']}");
        $closeTime = strtotime("{$date} {$day['close']}");

        $slots = [];
        $cur   = $openTime;

        while ($cur + ($duration * 60) <= $closeTime) {
            $slots[] = [
                'start' => date('H:i', $cur),
                'end'   => date('H:i', $cur + $duration * 60),
            ];
            $cur += $duration * 60;
        }

        return $slots;
    }

    /**
     * Return only the slots that are NOT already booked or blocked.
     * Skips past slots when the date is today.
     *
     * @return array<int, array{start: string, end: string}>
     */
    public function availableSlots(string $date): array
    {
        $all = $this->allSlots($date);
        if (empty($all)) {
            return [];
        }

        // Fetch booked (active) appointments for this doctor on this date
        $booked = Appointment::forDate($date)
            ->forDoctor($this->id)
            ->active()
            ->get(['slot_start', 'slot_end'])
            ->toArray();

        // Fetch manually blocked ranges for this date
        $blocked = $this->blockedSlots()
            ->where('blocked_date', $date)
            ->get(['slot_start', 'slot_end', 'is_full_day'])
            ->toArray();

        // If any full-day block exists, return no slots
        foreach ($blocked as $b) {
            if ($b['is_full_day']) {
                return [];
            }
        }

        $isToday = $date === date('Y-m-d');
        $nowPlus = time() + 3600; // must be at least 1 hour from now

        return array_values(array_filter($all, function (array $slot) use ($booked, $blocked, $isToday, $nowPlus, $date): bool {
            // Skip past / too-soon slots when date is today
            if ($isToday && strtotime("{$date} {$slot['start']}") <= $nowPlus) {
                return false;
            }

            // Check against booked appointments (overlap logic)
            foreach ($booked as $b) {
                if ($slot['start'] < $b['slot_end'] && $slot['end'] > $b['slot_start']) {
                    return false;
                }
            }

            // Check against manual blocks
            foreach ($blocked as $b) {
                if ($b['slot_start'] && $b['slot_end']) {
                    if ($slot['start'] < $b['slot_end'] && $slot['end'] > $b['slot_start']) {
                        return false;
                    }
                }
            }

            return true;
        }));
    }

    /**
     * Alias kept for backwards compatibility with Phase 1 calls.
     */
    public function generateSlots(string $date): array
    {
        return $this->allSlots($date);
    }
}
