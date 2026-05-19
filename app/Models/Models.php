<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/* ════════════════════════════════════════════════════════════
   SERVICE MODEL
   ════════════════════════════════════════════════════════════ */

class Service extends Model
{
    protected $fillable = [
        'name', 'name_ar', 'slug', 'icon', 'category',
        'short_description', 'description', 'benefits', 'image',
        'price_from', 'price_to', 'price_label', 'duration_minutes',
        'meta_title', 'meta_description',
        'is_featured', 'is_bookable', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'benefits'    => 'array',
        'is_featured' => 'boolean',
        'is_bookable' => 'boolean',
        'is_active'   => 'boolean',
        'price_from'  => 'decimal:2',
        'price_to'    => 'decimal:2',
    ];

    /* ── Boot ───────────────────────────────────────────────── */
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /* ── Relationships ──────────────────────────────────────── */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true)->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeBookable(Builder $q): Builder
    {
        return $q->where('is_bookable', true)->where('is_active', true)->orderBy('sort_order');
    }

    /* ── Accessors ──────────────────────────────────────────── */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/service-placeholder.jpg');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

/* ════════════════════════════════════════════════════════════
   APPOINTMENT MODEL
   ════════════════════════════════════════════════════════════ */

class Appointment extends Model
{
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

    protected $fillable = [
        'reference', 'doctor_id', 'service_id', 'user_id', 'offer_id',
        'patient_name', 'patient_phone', 'patient_email',
        'appointment_date', 'slot_start', 'slot_end',
        'status', 'source', 'notes', 'admin_notes',
        'confirmed_by', 'cancelled_by', 'cancellation_reason',
        'confirmed_at', 'cancelled_at', 'completed_at', 'reminder_sent_at',
        'price_quoted', 'price_paid', 'payment_status', 'payment_method',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'confirmed_at'     => 'datetime',
        'cancelled_at'     => 'datetime',
        'completed_at'     => 'datetime',
        'reminder_sent_at' => 'datetime',
        'price_quoted'     => 'decimal:2',
        'price_paid'       => 'decimal:2',
    ];

    /* ── Relationships ──────────────────────────────────────── */
    public function doctor(): BelongsTo   { return $this->belongsTo(Doctor::class); }
    public function service(): BelongsTo  { return $this->belongsTo(Service::class); }
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function offer(): BelongsTo    { return $this->belongsTo(Offer::class); }

    /* ── Scopes ─────────────────────────────────────────────── */
    public function scopePending(Builder $q): Builder   { return $q->where('status', self::STATUS_PENDING); }
    public function scopeConfirmed(Builder $q): Builder { return $q->where('status', self::STATUS_CONFIRMED); }
    public function scopeToday(Builder $q): Builder     { return $q->whereDate('appointment_date', today()); }

    public function scopeForDate(Builder $q, string $date): Builder
    {
        return $q->where('appointment_date', $date);
    }

    public function scopeForDoctor(Builder $q, int $doctorId): Builder
    {
        return $q->where('doctor_id', $doctorId);
    }

    public function scopeNotCancelled(Builder $q): Builder
    {
        return $q->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_NO_SHOW]);
    }

    /* ── Helpers ────────────────────────────────────────────── */
    public static function generateReference(): string
    {
        do {
            $ref = 'SDC-' . date('Y') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (self::where('reference', $ref)->exists());
        return $ref;
    }

    public function isSlotAvailable(): bool
    {
        return !self::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $this->appointment_date)
            ->where('id', '!=', $this->id ?? 0)
            ->notCancelled()
            ->where('slot_start', '<', $this->slot_end)
            ->where('slot_end', '>', $this->slot_start)
            ->exists();
    }
}

/* ════════════════════════════════════════════════════════════
   TESTIMONIAL MODEL
   ════════════════════════════════════════════════════════════ */

class Testimonial extends Model
{
    protected $fillable = [
        'user_id', 'appointment_id', 'name', 'avatar', 'rating',
        'title', 'review', 'service', 'source', 'google_review_url',
        'is_featured', 'is_approved', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function user(): BelongsTo        { return $this->belongsTo(User::class); }
    public function appointment(): BelongsTo { return $this->belongsTo(Appointment::class); }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_active', true)->where('is_approved', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->published()->where('is_featured', true);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }
}

/* ════════════════════════════════════════════════════════════
   GALLERY MODEL
   ════════════════════════════════════════════════════════════ */

class Gallery extends Model
{
    protected $table = 'galleries';

    protected $fillable = [
        'service_id', 'type', 'title', 'description',
        'before_image', 'after_image', 'image',
        'treatment', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }

    public function scopeActive(Builder $q): Builder      { return $q->where('is_active', true); }
    public function scopeBeforeAfter(Builder $q): Builder { return $q->where('type', 'before_after'); }

    public function getBeforeImageUrlAttribute(): string
    {
        return $this->before_image ? asset('storage/' . $this->before_image) : asset('images/placeholder.jpg');
    }

    public function getAfterImageUrlAttribute(): string
    {
        return $this->after_image ? asset('storage/' . $this->after_image) : asset('images/placeholder.jpg');
    }
}

/* ════════════════════════════════════════════════════════════
   OFFER MODEL
   ════════════════════════════════════════════════════════════ */

class Offer extends Model
{
    protected $fillable = [
        'service_id', 'title', 'title_ar', 'description', 'image',
        'original_price', 'offer_price', 'badge_text', 'terms',
        'starts_at', 'expires_at', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'offer_price'    => 'decimal:2',
        'starts_at'      => 'datetime',
        'expires_at'     => 'datetime',
        'is_featured'    => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
                 ->where('starts_at', '<=', now())
                 ->where(fn($s) => $s->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->original_price || $this->original_price == 0) return 0;
        return (int) round((1 - $this->offer_price / $this->original_price) * 100);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/offer-placeholder.jpg');
    }
}

/* ════════════════════════════════════════════════════════════
   FAQ MODEL
   ════════════════════════════════════════════════════════════ */

class Faq extends Model
{
    protected $fillable = [
        'category', 'question', 'question_ar', 'answer', 'answer_ar',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder   { return $q->where('is_active', true)->orderBy('sort_order'); }
    public function scopeFeatured(Builder $q): Builder { return $q->active()->where('is_featured', true); }

    public function scopeByCategory(Builder $q, string $cat): Builder
    {
        return $q->active()->where('category', $cat);
    }
}
