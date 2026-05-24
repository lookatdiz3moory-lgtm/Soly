<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Testimonial Model
 *
 * Patient reviews, sourced from internal submission, Google,
 * Facebook, or WhatsApp screenshots.
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property int|null    $appointment_id
 * @property string      $name
 * @property string|null $avatar         — storage path
 * @property int         $rating         — 1–5
 * @property string|null $title
 * @property string      $review
 * @property string|null $service
 * @property string      $source         — internal|google|facebook|whatsapp
 * @property string|null $google_review_url
 * @property bool        $is_featured
 * @property bool        $is_approved
 * @property bool        $is_active
 * @property int         $sort_order
 */
class Testimonial extends Model
{
    use HasLocalizedAttributes;

    public const SOURCE_INTERNAL  = 'internal';
    public const SOURCE_GOOGLE    = 'google';
    public const SOURCE_FACEBOOK  = 'facebook';
    public const SOURCE_WHATSAPP  = 'whatsapp';

    protected $fillable = [
        'user_id',
        'appointment_id',
        'name',
        'name_ar',
        'avatar',
        'rating',
        'title',
        'review',
        'review_ar',
        'service',
        'source',
        'google_review_url',
        'is_featured',
        'is_approved',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /* ── Scopes ─────────────────────────────────────────────── */

    /**
     * Approved and active testimonials (public-facing).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('is_approved', true);
    }

    /**
     * Featured published testimonials.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->published()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('rating');
    }

    /**
     * Filter by minimum star rating.
     */
    public function scopeMinRating(Builder $query, int $stars = 4): Builder
    {
        return $query->where('rating', '>=', $stars);
    }

    /**
     * Pending approval — for admin review queue.
     */
    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_approved', false);
    }

    /* ── Accessors ──────────────────────────────────────────── */

    /**
     * Locale-aware accessors. EN fields are the source of truth;
     * AR values are used only when locale=ar AND the AR field is non-empty.
     */
    public function getLocalizedNameAttribute(): ?string
    {
        return $this->localized('name');
    }

    public function getLocalizedReviewAttribute(): ?string
    {
        return $this->localized('review');
    }

    /**
     * Full public URL for the avatar image.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : null;
    }

    /**
     * Rendered star string: ★★★★★ or ★★★★☆
     */
    public function getStarsHtmlAttribute(): string
    {
        $filled = str_repeat('★', (int)$this->rating);
        $empty  = str_repeat('☆', 5 - (int)$this->rating);
        return $filled . $empty;
    }

    /**
     * First letter of the reviewer's name, for avatar placeholder.
     */
    public function getInitialAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->localized_name ?? '', 0, 1));
    }

    /**
     * Truncated review for card previews. Locale-aware.
     */
    public function getExcerptAttribute(): string
    {
        return \Str::limit((string) $this->localized_review, 180);
    }
}
