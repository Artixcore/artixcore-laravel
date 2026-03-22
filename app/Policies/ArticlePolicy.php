<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function view(?User $user, Article $article): bool
    {
        if ($article->status !== 'published') {
            return false;
        }

        return ! $article->published_at || $article->published_at->lte(now());
    }
}
