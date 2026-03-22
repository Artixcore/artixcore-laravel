<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('articles.view_any');
    }

    public function view(?User $user, Article $article): bool
    {
        if ($user !== null && $user->can('articles.view')) {
            return true;
        }

        if ($article->status !== 'published') {
            return false;
        }

        return ! $article->published_at || $article->published_at->lte(now());
    }

    public function create(User $user): bool
    {
        return $user->can('articles.create');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->can('articles.update');
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->can('articles.delete');
    }
}
