<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentRelation extends Model
{
    public const RELATED_ARTICLE = 'related_article';

    public const RELATED_CASE_STUDY = 'related_case_study';

    public const RELATED_PORTFOLIO = 'related_portfolio';

    public const RELATED_SERVICE = 'related_service';

    public const RELATED_PLATFORM = 'related_platform';

    protected $fillable = [
        'source_type',
        'source_id',
        'related_type',
        'related_id',
        'relation_type',
        'sort_order',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
