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

        if ($article->status === Article::STATUS_ARCHIVED) {
            return false;
        }

        if ($article->status !== Article::STATUS_PUBLISHED) {
            return false;
        }

        return $article->published_at !== null && $article->published_at->lte(now());
    }

    public function publish(User $user, Article $article): bool
    {
        return $user->can('articles.publish');
    }

    public function generateAi(User $user): bool
    {
        return $user->can('ai_articles.generate');
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
