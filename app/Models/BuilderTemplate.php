<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuilderTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'document_json',
        'thumbnail_media_id',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'document_json' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<MediaAsset, $this>
     */
    public function thumbnailMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'thumbnail_media_id');
    }
}
