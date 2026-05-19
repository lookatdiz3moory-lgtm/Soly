<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * BlockedSlot Model
 *
 * Allows admin/secretary to block individual or full-day time ranges
 * per doctor (e.g. holidays, personal breaks, clinic closures).
 *
 * @property int         $id
 * @property int|null    $doctor_id     — null = block all doctors that day
 * @property string      $blocked_date  — Y-m-d
 * @property string|null $slot_start    — HH:MM (null = full day)
 * @property string|null $slot_end      — HH:MM (null = full day)
 * @property bool        $is_full_day
 * @property string|null $reason
 * @property int|null    $created_by    — User id
 */
class BlockedSlot extends Model
{
    protected $fillable = [
        'doctor_id',
        'blocked_date',
        'slot_start',
        'slot_end',
        'is_full_day',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'blocked_date' => 'date:Y-m-d',
        'is_full_day'  => 'boolean',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('blocked_date', $date);
    }

    public function scopeForDoctor(Builder $query, int $doctorId): Builder
    {
        return $query->where(function (Builder $q) use ($doctorId): void {
            $q->where('doctor_id', $doctorId)->orWhereNull('doctor_id');
        });
    }
}
