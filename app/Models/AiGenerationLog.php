<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationLog extends Model
{
    protected $fillable = [
        'user_id',
        'page_id',
        'page_version_id',
        'ai_provider_id',
        'action',
        'request_summary',
        'prompt_tokens',
        'completion_tokens',
        'status',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * @return BelongsTo<PageVersion, $this>
     */
    public function pageVersion(): BelongsTo
    {
        return $this->belongsTo(PageVersion::class);
    }

    /**
     * @return BelongsTo<AiProvider, $this>
     */
    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }
}
