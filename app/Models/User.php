<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * User Model
 *
 * Covers both admin staff (role != patient) and registered patients.
 *
 * Roles:
 *   superadmin — full clinic access
 *   admin      — manage all content and bookings
 *   secretary  — manage bookings and patients only
 *   doctor     — view own schedule
 *   patient    — book and view own appointments
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property string      $role
 * @property string|null $phone
 * @property string|null $avatar
 * @property bool        $is_active
 * @property \Carbon\Carbon|null $email_verified_at
 */
class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN      = 'admin';
    public const ROLE_SECRETARY  = 'secretary';
    public const ROLE_DOCTOR     = 'doctor';
    public const ROLE_PATIENT    = 'patient';

    public const STAFF_ROLES = [
        self::ROLE_SUPERADMIN,
        self::ROLE_ADMIN,
        self::ROLE_SECRETARY,
        self::ROLE_DOCTOR,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    /**
     * All appointments linked to this user (patient bookings).
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Testimonials submitted by this user.
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->whereIn('role', self::STAFF_ROLES);
    }

    public function scopePatients(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_PATIENT);
    }

    /* ── Role Helpers ───────────────────────────────────────── */

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN], true);
    }

    public function isSecretary(): bool
    {
        return $this->role === self::ROLE_SECRETARY;
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, self::STAFF_ROLES, true);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /* ── Accessors ──────────────────────────────────────────── */

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/avatar-placeholder.svg');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => 'Super Admin',
            self::ROLE_ADMIN      => 'Admin',
            self::ROLE_SECRETARY  => 'Secretary',
            self::ROLE_DOCTOR     => 'Doctor',
            self::ROLE_PATIENT    => 'Patient',
            default               => ucfirst($this->role),
        };
    }
}
