<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Appointment Model
 *
 * Core booking record. One appointment = one patient visit slot.
 *
 * Status workflow:
 *   pending → confirmed → completed
 *   pending → cancelled
 *   confirmed → cancelled | no_show | rescheduled
 *
 * @property int         $id
 * @property string      $reference     — public-facing ref e.g. SDC-2024XXXXX
 * @property int         $doctor_id
 * @property int         $service_id
 * @property int|null    $user_id
 * @property int|null    $offer_id
 * @property string      $patient_name
 * @property string      $patient_phone
 * @property string|null $patient_email
 * @property \Carbon\Carbon $appointment_date
 * @property string      $slot_start    — HH:MM
 * @property string      $slot_end      — HH:MM
 * @property string      $status
 * @property string      $source
 */
class Appointment extends Model
{
    /* ── Status constants ───────────────────────────────────── */

    public const STATUS_PENDING     = 'pending';
    public const STATUS_CONFIRMED   = 'confirmed';
    public const STATUS_CANCELLED   = 'cancelled';
    public const STATUS_COMPLETED   = 'completed';
    public const STATUS_NO_SHOW     = 'no_show';
    public const STATUS_RESCHEDULED = 'rescheduled';

    public const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
        self::STATUS_NO_SHOW,
        self::STATUS_RESCHEDULED,
    ];

    /** Statuses that still occupy a time slot */
    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
    ];

    /* ── Source constants ───────────────────────────────────── */

    public const SOURCE_WEBSITE  = 'website';
    public const SOURCE_WHATSAPP = 'whatsapp';
    public const SOURCE_PHONE    = 'phone';
    public const SOURCE_WALKIN   = 'walkin';
    public const SOURCE_ADMIN    = 'admin';

    /* ── Fillable ───────────────────────────────────────────── */

    protected $fillable = [
        'reference',
        'doctor_id',
        'service_id',
        'user_id',
        'offer_id',
        'patient_name',
        'patient_phone',
        'patient_email',
        'appointment_date',
        'slot_start',
        'slot_end',
        'status',
        'source',
        'notes',
        'admin_notes',
        'confirmed_by',
        'cancelled_by',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
        'reminder_sent_at',
        'price_quoted',
        'price_paid',
        'payment_status',
        'payment_method',
    ];

    protected $casts = [
        'appointment_date' => 'date:Y-m-d',
        'confirmed_at'     => 'datetime',
        'cancelled_at'     => 'datetime',
        'completed_at'     => 'datetime',
        'reminder_sent_at' => 'datetime',
        'price_quoted'     => 'decimal:2',
        'price_paid'       => 'decimal:2',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_COMPLETED);
    }

    public function scopeToday(Builder $q): Builder
    {
        return $q->whereDate('appointment_date', today());
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->whereDate('appointment_date', '>=', today())
                 ->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeForDate(Builder $q, string $date): Builder
    {
        return $q->where('appointment_date', $date);
    }

    public function scopeForDoctor(Builder $q, int $doctorId): Builder
    {
        return $q->where('doctor_id', $doctorId);
    }

    /**
     * Exclude cancelled and no-show records — these don't occupy a slot.
     */
    public function scopeActive(Builder $q): Builder
    {
        return $q->whereIn('status', self::ACTIVE_STATUSES);
    }

    /**
     * Alias kept for backwards compatibility.
     */
    public function scopeNotCancelled(Builder $q): Builder
    {
        return $q->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_NO_SHOW]);
    }

    /* ── Static Helpers ─────────────────────────────────────── */

    /**
     * Generate a unique booking reference: SDC-YYYY + 5 random uppercase chars.
     */
    public static function generateReference(): string
    {
        do {
            $ref = 'SDC-' . date('Y') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    /**
     * Check whether the slot stored on this instance is still free.
     * Call before inserting a new appointment.
     */
    public function isSlotAvailable(): bool
    {
        return !static::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $this->appointment_date)
            ->where('id', '!=', $this->id ?? 0)
            ->active()
            ->where('slot_start', '<', $this->slot_end)
            ->where('slot_end',   '>', $this->slot_start)
            ->exists();
    }

    /* ── Accessors ──────────────────────────────────────────── */

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'Pending',
            self::STATUS_CONFIRMED   => 'Confirmed',
            self::STATUS_CANCELLED   => 'Cancelled',
            self::STATUS_COMPLETED   => 'Completed',
            self::STATUS_NO_SHOW     => 'No Show',
            self::STATUS_RESCHEDULED => 'Rescheduled',
            default                  => ucfirst($this->status),
        };
    }

    /**
     * Tailwind / CSS colour token for the status.
     * Used by admin views.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING     => 'warning',
            self::STATUS_CONFIRMED   => 'success',
            self::STATUS_CANCELLED   => 'error',
            self::STATUS_COMPLETED   => 'info',
            self::STATUS_NO_SHOW     => 'muted',
            self::STATUS_RESCHEDULED => 'primary',
            default                  => 'muted',
        };
    }

    /**
     * Formatted slot range: "9:00 AM – 9:30 AM"
     */
    public function getSlotRangeAttribute(): string
    {
        $fmt = fn(string $t) => date('g:i A', strtotime("2000-01-01 {$t}"));
        return $fmt($this->slot_start) . ' – ' . $fmt($this->slot_end);
    }
}
