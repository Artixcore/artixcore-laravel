<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleGenerationLog extends Model
{
    protected $fillable = [
        'log_date',
        'status',
        'article_type',
        'content_type',
        'error_message',
        'payload_summary',
        'articles_created',
        'records_created',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'articles_created' => 'integer',
            'records_created' => 'integer',
            'metadata' => 'array',
        ];
    }
}
