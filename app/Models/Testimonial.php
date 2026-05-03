<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $fillable = [
        'author_name',
        'role',
        'company',
        'submitter_email',
        'body',
        'rating',
        'avatar_media_id',
        'company_logo_media_id',
        'service_id',
        'product_id',
        'portfolio_item_id',
        'case_study_id',
        'crm_contact_id',
        'sort_order',
        'is_published',
        'featured',
        'moderation_status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'featured' => 'boolean',
            'rating' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function avatarMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'avatar_media_id');
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function companyLogoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'company_logo_media_id');
    }

    /**
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('moderation_status', 'approved')
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
