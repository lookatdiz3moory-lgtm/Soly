<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Gallery Model
 *
 * Stores before/after pairs and general clinic images.
 *
 * @property int         $id
 * @property int|null    $service_id
 * @property string      $type          — before_after|general|clinic|team
 * @property string|null $title
 * @property string|null $description
 * @property string|null $before_image  — storage path (before_after type)
 * @property string|null $after_image   — storage path (before_after type)
 * @property string|null $image         — storage path (general/clinic/team)
 * @property string|null $treatment
 * @property bool        $is_featured
 * @property bool        $is_active
 * @property int         $sort_order
 */
class Gallery extends Model
{
    protected $table = 'galleries';

    public const TYPE_BEFORE_AFTER = 'before_after';
    public const TYPE_GENERAL      = 'general';
    public const TYPE_CLINIC       = 'clinic';
    public const TYPE_TEAM         = 'team';

    protected $fillable = [
        'service_id',
        'type',
        'title',
        'description',
        'before_image',
        'after_image',
        'image',
        'treatment',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeBeforeAfter(Builder $query): Builder
    {
        return $query->active()->where('type', self::TYPE_BEFORE_AFTER);
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->active()->where('type', self::TYPE_GENERAL);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->active()->where('is_featured', true);
    }

    /* ── Accessors ──────────────────────────────────────────── */

    public function getBeforeImageUrlAttribute(): string
    {
        if (!$this->before_image) {
            return asset('images/placeholder-ba.svg');
        }
        if (str_starts_with($this->before_image, 'images/')) {
            return asset($this->before_image);
        }
        return asset('storage/' . $this->before_image);
    }

    public function getAfterImageUrlAttribute(): string
    {
        if (!$this->after_image) {
            return asset('images/placeholder-ba.svg');
        }
        if (str_starts_with($this->after_image, 'images/')) {
            return asset($this->after_image);
        }
        return asset('storage/' . $this->after_image);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/placeholder.svg');
        }
        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }
}
