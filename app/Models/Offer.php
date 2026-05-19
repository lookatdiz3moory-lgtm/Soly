<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Offer Model
 *
 * Time-limited promotional pricing for services.
 *
 * @property int         $id
 * @property int|null    $service_id
 * @property string      $title
 * @property string|null $title_ar
 * @property string|null $description
 * @property string|null $image
 * @property float       $original_price
 * @property float       $offer_price
 * @property string|null $badge_text     — e.g. "Limited Time"
 * @property string|null $terms
 * @property \Carbon\Carbon $starts_at
 * @property \Carbon\Carbon|null $expires_at
 * @property bool        $is_featured
 * @property bool        $is_active
 * @property int         $sort_order
 */
class Offer extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'title_ar',
        'description',
        'image',
        'original_price',
        'offer_price',
        'badge_text',
        'terms',
        'starts_at',
        'expires_at',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'offer_price'    => 'decimal:2',
        'starts_at'      => 'datetime',
        'expires_at'     => 'datetime',
        'is_featured'    => 'boolean',
        'is_active'      => 'boolean',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    /**
     * Currently live offers (active, started, not expired).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function (Builder $q): void {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->active()->where('is_featured', true)->orderBy('sort_order');
    }

    /* ── Accessors ──────────────────────────────────────────── */

    /**
     * Calculated discount percentage (0–100).
     */
    public function getDiscountPercentAttribute(): int
    {
        if (!$this->original_price || (float)$this->original_price === 0.0) {
            return 0;
        }

        return (int) round(
            (1 - (float)$this->offer_price / (float)$this->original_price) * 100
        );
    }

    /**
     * Monetary saving amount.
     */
    public function getSavingAttribute(): float
    {
        return max(0, (float)$this->original_price - (float)$this->offer_price);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/offer-placeholder.jpg');
    }

    /**
     * Whether the offer is currently live.
     */
    public function getIsLiveAttribute(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at > now()) return false;
        if ($this->expires_at && $this->expires_at < now()) return false;
        return true;
    }
}
