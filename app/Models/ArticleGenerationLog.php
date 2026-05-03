<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleGenerationLog extends Model
{
    protected $fillable = [
        'log_date',
        'status',
        'article_type',
        'error_message',
        'articles_created',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'articles_created' => 'integer',
            'metadata' => 'array',
        ];
    }
}
