<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * FAQ Model
 *
 * @property int         $id
 * @property string      $category
 * @property string      $question
 * @property string|null $question_ar
 * @property string      $answer
 * @property string|null $answer_ar
 * @property bool        $is_featured
 * @property bool        $is_active
 * @property int         $sort_order
 */
class Faq extends Model
{
    protected $fillable = [
        'category',
        'question',
        'question_ar',
        'answer',
        'answer_ar',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /* ── Scopes ─────────────────────────────────────────────── */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->active()->where('is_featured', true);
    }

    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->active()->where('category', $category);
    }

    /**
     * Return all active FAQs grouped by category.
     *
     * @return array<string, \Illuminate\Database\Eloquent\Collection>
     */
    public static function grouped(): array
    {
        return static::active()
            ->get()
            ->groupBy('category')
            ->toArray();
    }
}
