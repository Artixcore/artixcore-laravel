<?php

namespace App\Services\Content;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Product;
use App\Models\ResearchPaper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class RelatedContentService
{
    /**
     * @return Collection<int, Article>
     */
    public function relatedArticles(Article $article, int $limit = 4): Collection
    {
        $termIds = $article->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return Article::query()->whereKey([])->get();
        }

        return Article::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, ResearchPaper>
     */
    public function relatedResearchPapers(ResearchPaper $paper, int $limit = 4): Collection
    {
        $termIds = $paper->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return ResearchPaper::query()->whereKey([])->get();
        }

        return ResearchPaper::query()
            ->published()
            ->where('id', '!=', $paper->id)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, CaseStudy>
     */
    public function relatedCaseStudies(CaseStudy $study, int $limit = 4): Collection
    {
        $termIds = $study->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return CaseStudy::query()->whereKey([])->get();
        }

        return CaseStudy::query()
            ->published()
            ->where('id', '!=', $study->id)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    public function relatedProducts(Product $product, int $limit = 4): Collection
    {
        $termIds = $product->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return Product::query()->whereKey([])->get();
        }

        return Product::query()
            ->published()
            ->where('id', '!=', $product->id)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('sort_order')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  BaseCollection<int, string>  $topicSlugs
     * @return \Illuminate\Database\Eloquent\Builder<Article>
     */
    public function applyArticleInterestBoost($query, BaseCollection $topicSlugs): void
    {
        if ($topicSlugs->isEmpty()) {
            return;
        }

        $taxonomy = \App\Models\Taxonomy::query()->where('slug', 'topics')->first();
        if (! $taxonomy) {
            return;
        }

        $termIds = \App\Models\Term::query()
            ->where('taxonomy_id', $taxonomy->id)
            ->whereIn('slug', $topicSlugs->all())
            ->pluck('id');

        if ($termIds->isEmpty()) {
            return;
        }

        $ids = $termIds->all();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $query->orderByRaw(
            "(SELECT COUNT(*) FROM termables WHERE termables.termable_id = articles.id AND termables.termable_type = ? AND termables.term_id IN ({$placeholders})) DESC",
            array_merge([Article::class], $ids)
        );
    }
}
