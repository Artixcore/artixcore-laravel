<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    protected $fillable = [
        'disk',
        'directory',
        'path',
        'filename',
        'mime_type',
        'size_bytes',
        'alt_text',
        'caption',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'size_bytes' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Absolute URL for public API / frontends (cross-origin safe).
     */
    public function absoluteUrl(): string
    {
        $relative = Storage::disk($this->disk)->url($this->path);

        return url($relative);
    }
}
