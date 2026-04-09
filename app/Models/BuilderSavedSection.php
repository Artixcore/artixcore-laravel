<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuilderSavedSection extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'document_json',
    ];

    protected function casts(): array
    {
        return [
            'document_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
