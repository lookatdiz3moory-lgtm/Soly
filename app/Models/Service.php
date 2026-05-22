<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Service Model
 *
 * Represents a dental or cosmetic service offered by the clinic.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $name_ar
 * @property string      $slug
 * @property string|null $icon
 * @property string|null $category
 * @property string|null $short_description
 * @property string|null $description
 * @property array|null  $benefits
 * @property string|null $image
 * @property float|null  $price_from
 * @property float|null  $price_to
 * @property string|null $price_label
 * @property int|null    $duration_minutes
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property bool        $is_featured
 * @property bool        $is_bookable
 * @property bool        $is_active
 * @property int         $sort_order
 */
class Service extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'icon',
        'category',
        'short_description',
        'description',
        'benefits',
        'image',
        'price_from',
        'price_to',
        'price_label',
        'duration_minutes',
        'meta_title',
        'meta_description',
        'is_featured',
        'is_bookable',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'benefits'    => 'array',
        'price_from'  => 'decimal:2',
        'price_to'    => 'decimal:2',
        'is_featured' => 'boolean',
        'is_bookable' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /* ── Boot ───────────────────────────────────────────────── */

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug from name on create if not provided
        static::creating(function (self $model): void {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        // Keep slug in sync when name changes and no explicit slug given
        static::updating(function (self $model): void {
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /* ── Relationships ──────────────────────────────────────── */

    /**
     * Doctors who perform this service.
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class);
    }

    /**
     * All appointments booked for this service.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Gallery items tagged to this service.
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Offers linked to this service.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    /**
     * Active services, ordered by sort_order.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Featured active services.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Bookable active services (shown on booking form dropdown).
     */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('is_bookable', true)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Filter by category string.
     */
    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /* ── Accessors ──────────────────────────────────────────── */

    /**
     * Full public URL for the service image.
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/service-placeholder.svg');
        }
        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }

    /**
     * Formatted price label, falling back to generated string.
     */
    public function getPriceLabelTextAttribute(): string
    {
        if ($this->price_label) {
            return $this->price_label;
        }

        if ($this->price_from) {
            return 'From ' . number_format((float)$this->price_from) . ' EGP';
        }

        return '';
    }

    /* ── Route Model Binding ────────────────────────────────── */

    /**
     * Resolve route model binding by slug instead of id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
