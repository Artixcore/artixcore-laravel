<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'seed_key',
        'question',
        'answer',
        'category',
        'sort_order',
        'is_published',
        'status',
        'is_featured',
        'show_on_general_faq',
        'show_on_saas_page',
        'meta_title',
        'meta_description',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'show_on_general_faq' => 'boolean',
            'show_on_saas_page' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->where('status', 'published')
                ->orWhere(function (Builder $q2): void {
                    $q2->whereNull('status')->where('is_published', true);
                });
        });
    }

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
