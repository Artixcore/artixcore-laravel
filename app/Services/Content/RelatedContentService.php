<?php

namespace App\Services\Content;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\ContentRelation;
use App\Models\MarketUpdate;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\ResearchPaper;
use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;

class RelatedContentService
{
    /**
     * Curated + taxonomy fallback related articles.
     *
     * @return Collection<int, Article>
     */
    public function relatedArticles(Article $article, int $limit = 4): Collection
    {
        $explicit = $this->explicitOutgoingModels(
            $article,
            Article::class,
            ContentRelation::RELATED_ARTICLE,
            $limit,
            fn (Builder $q) => $q->published()->where('articles.id', '!=', $article->id)
        );

        if ($explicit->count() >= $limit) {
            return $explicit->take($limit)->values();
        }

        $excludeIds = $explicit->pluck('id')->push($article->id)->all();
        $remaining = $limit - $explicit->count();

        return $explicit->concat(
            $this->termOverlapArticles($article, $remaining, $excludeIds)
        )->take($limit)->values();
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
     * Curated + taxonomy fallback related case studies.
     *
     * @return Collection<int, CaseStudy>
     */
    public function relatedCaseStudies(CaseStudy $study, int $limit = 4): Collection
    {
        $explicit = $this->explicitOutgoingModels(
            $study,
            CaseStudy::class,
            ContentRelation::RELATED_CASE_STUDY,
            $limit,
            fn (Builder $q) => $q->published()->where('case_studies.id', '!=', $study->id)
        );

        if ($explicit->count() >= $limit) {
            return $explicit->take($limit)->values();
        }

        $excludeIds = $explicit->pluck('id')->push($study->id)->all();
        $remaining = $limit - $explicit->count();

        return $explicit->concat(
            $this->termOverlapCaseStudies($study, $remaining, $excludeIds)
        )->take($limit)->values();
    }

    /**
     * @return Collection<int, MarketUpdate>
     */
    public function relatedMarketUpdates(MarketUpdate $update, int $limit = 4): Collection
    {
        $termIds = $update->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return MarketUpdate::query()->whereKey([])->get();
        }

        return MarketUpdate::query()
            ->published()
            ->where('id', '!=', $update->id)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Curated + taxonomy fallback related products.
     *
     * @return Collection<int, Product>
     */
    public function relatedProducts(Product $product, int $limit = 4): Collection
    {
        $explicit = $this->explicitOutgoingModels(
            $product,
            Product::class,
            ContentRelation::RELATED_PLATFORM,
            $limit,
            fn (Builder $q) => $q->published()->where('products.id', '!=', $product->id)
        );

        if ($explicit->count() >= $limit) {
            return $explicit->take($limit)->values();
        }

        $excludeIds = $explicit->pluck('id')->push($product->id)->all();
        $remaining = $limit - $explicit->count();

        return $explicit->concat(
            $this->termOverlapProducts($product, $remaining, $excludeIds)
        )->take($limit)->values();
    }

    /**
     * @return array{
     *     articles: Collection<int, Article>,
     *     caseStudies: Collection<int, CaseStudy>,
     *     portfolioItems: Collection<int, PortfolioItem>
     * }
     */
    public function bundleForService(Service $service, int $limit = 12): array
    {
        return [
            'articles' => $this->explicitOutgoingModels(
                $service,
                Article::class,
                ContentRelation::RELATED_ARTICLE,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'caseStudies' => $this->explicitOutgoingModels(
                $service,
                CaseStudy::class,
                ContentRelation::RELATED_CASE_STUDY,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'portfolioItems' => $this->explicitOutgoingModels(
                $service,
                PortfolioItem::class,
                ContentRelation::RELATED_PORTFOLIO,
                $limit,
                fn (Builder $q) => $q->published()
            ),
        ];
    }

    /**
     * @return array{
     *     articles: Collection<int, Article>,
     *     caseStudies: Collection<int, CaseStudy>
     * }
     */
    public function bundleForProduct(Product $product, int $limit = 12): array
    {
        return [
            'articles' => $this->explicitOutgoingModels(
                $product,
                Article::class,
                ContentRelation::RELATED_ARTICLE,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'caseStudies' => $this->explicitOutgoingModels(
                $product,
                CaseStudy::class,
                ContentRelation::RELATED_CASE_STUDY,
                $limit,
                fn (Builder $q) => $q->published()
            ),
        ];
    }

    /**
     * @return array{
     *     services: Collection<int, Service>,
     *     articles: Collection<int, Article>,
     *     caseStudies: Collection<int, CaseStudy>
     * }
     */
    public function bundleForPortfolioItem(PortfolioItem $item, int $limit = 12): array
    {
        $fromPortfolio = $this->explicitOutgoingModels(
            $item,
            Service::class,
            ContentRelation::RELATED_SERVICE,
            $limit,
            fn (Builder $q) => $q->published()
        );
        $fromServices = $this->incomingSourceModels(
            $item,
            Service::class,
            ContentRelation::RELATED_PORTFOLIO,
            $limit,
            fn (Builder $q) => $q->published()
        );

        return [
            'services' => $fromPortfolio->concat($fromServices)->unique('id')->take($limit)->values(),
            'articles' => $this->explicitOutgoingModels(
                $item,
                Article::class,
                ContentRelation::RELATED_ARTICLE,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'caseStudies' => $this->explicitOutgoingModels(
                $item,
                CaseStudy::class,
                ContentRelation::RELATED_CASE_STUDY,
                $limit,
                fn (Builder $q) => $q->published()
            ),
        ];
    }

    /**
     * Services / platforms linked from an article via content graph.
     *
     * @return array{services: Collection<int, Service>, platforms: Collection<int, Product>}
     */
    /**
     * @return Collection<int, CaseStudy>
     */
    public function relatedCaseStudiesForArticle(Article $article, int $limit = 8): Collection
    {
        return $this->explicitOutgoingModels(
            $article,
            CaseStudy::class,
            ContentRelation::RELATED_CASE_STUDY,
            $limit,
            fn (Builder $q) => $q->published()
        );
    }

    public function linkedOwnersForArticle(Article $article, int $limit = 8): array
    {
        return [
            'services' => $this->incomingSourceModels(
                $article,
                Service::class,
                ContentRelation::RELATED_ARTICLE,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'platforms' => $this->incomingSourceModels(
                $article,
                Product::class,
                ContentRelation::RELATED_ARTICLE,
                $limit,
                fn (Builder $q) => $q->published()
            ),
        ];
    }

    /**
     * @return Collection<int, Article>
     */
    public function relatedArticlesForCaseStudyPage(CaseStudy $study, int $limit = 8): Collection
    {
        return $this->explicitOutgoingModels(
            $study,
            Article::class,
            ContentRelation::RELATED_ARTICLE,
            $limit,
            fn (Builder $q) => $q->published()
        );
    }

    /**
     * Portfolio + platform + service links for a case study detail page.
     *
     * @return array{
     *     services: Collection<int, Service>,
     *     platforms: Collection<int, Product>,
     *     portfolioItems: Collection<int, PortfolioItem>,
     *     articles: Collection<int, Article>
     * }
     */
    public function bundleForCaseStudy(CaseStudy $study, int $limit = 12): array
    {
        return [
            'services' => $this->incomingSourceModels(
                $study,
                Service::class,
                ContentRelation::RELATED_CASE_STUDY,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'platforms' => $this->incomingSourceModels(
                $study,
                Product::class,
                ContentRelation::RELATED_CASE_STUDY,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'portfolioItems' => $this->incomingSourceModels(
                $study,
                PortfolioItem::class,
                ContentRelation::RELATED_CASE_STUDY,
                $limit,
                fn (Builder $q) => $q->published()
            ),
            'articles' => $this->relatedArticlesForCaseStudyPage($study, $limit),
        ];
    }

    /**
     * @param  BaseCollection<int, string>  $topicSlugs
     */
    public function applyArticleInterestBoost($query, BaseCollection $topicSlugs): void
    {
        if ($topicSlugs->isEmpty()) {
            return;
        }

        $taxonomy = Taxonomy::query()->where('slug', 'topics')->first();
        if (! $taxonomy) {
            return;
        }

        $termIds = Term::query()
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

    /**
     * @template T of Model
     *
     * @param  class-string<T>  $relatedClass
     * @param  callable(Builder): Builder  $filterQuery
     * @return Collection<int, T>
     */
    private function explicitOutgoingModels(
        Model $source,
        string $relatedClass,
        ?string $relationType,
        int $limit,
        callable $filterQuery,
    ): Collection {
        $edgeQuery = ContentRelation::query()
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->where('related_type', $relatedClass);

        if ($relationType !== null) {
            $edgeQuery->where(function ($w) use ($relationType): void {
                $w->where('relation_type', $relationType)->orWhereNull('relation_type');
            });
        }

        /** @var Collection<int, ContentRelation> $rows */
        $rows = $edgeQuery->orderByDesc('is_featured')->orderBy('sort_order')->limit($limit)->get();

        return $this->hydrateOrdered($rows, $relatedClass, $filterQuery);
    }

    /**
     * @template T of Model
     *
     * @param  class-string<T>  $sourceClass
     * @param  callable(Builder): Builder  $filterQuery
     * @return Collection<int, T>
     */
    private function incomingSourceModels(
        Model $related,
        string $sourceClass,
        ?string $relationType,
        int $limit,
        callable $filterQuery,
    ): Collection {
        $edgeQuery = ContentRelation::query()
            ->where('related_type', $related::class)
            ->where('related_id', $related->getKey())
            ->where('source_type', $sourceClass);

        if ($relationType !== null) {
            $edgeQuery->where(function ($w) use ($relationType): void {
                $w->where('relation_type', $relationType)->orWhereNull('relation_type');
            });
        }

        /** @var Collection<int, ContentRelation> $rows */
        $rows = $edgeQuery->orderByDesc('is_featured')->orderBy('sort_order')->limit($limit)->get();

        return $this->hydrateIncomingSources($rows, $sourceClass, $filterQuery);
    }

    /**
     * @template T of Model
     *
     * @param  class-string<T>  $modelClass
     * @param  callable(Builder): Builder  $filterQuery
     * @return Collection<int, T>
     */
    private function hydrateOrdered(Collection $rows, string $modelClass, callable $filterQuery): Collection
    {
        $ids = $rows->pluck('related_id')->unique()->filter()->values()->all();
        if ($ids === []) {
            /** @var Collection<int, T> $empty */
            $empty = $modelClass::query()->whereRaw('1 = 0')->get();

            return $empty;
        }

        /** @var Builder $query */
        $query = $modelClass::query()->whereIn((new $modelClass)->getTable().'.id', $ids);
        $query = $filterQuery($query);
        /** @var Collection<int, T> $models */
        $models = $query->get()->keyBy('id');

        return $rows
            ->map(fn (ContentRelation $row) => $models->get($row->related_id))
            ->filter()
            ->values();
    }

    /**
     * @template T of Model
     *
     * @param  Collection<int, ContentRelation>  $rows
     * @param  class-string<T>  $modelClass
     * @param  callable(Builder): Builder  $filterQuery
     * @return Collection<int, T>
     */
    private function hydrateIncomingSources(Collection $rows, string $modelClass, callable $filterQuery): Collection
    {
        $ids = $rows->pluck('source_id')->unique()->filter()->values()->all();
        if ($ids === []) {
            /** @var Collection<int, T> $empty */
            $empty = $modelClass::query()->whereRaw('1 = 0')->get();

            return $empty;
        }

        /** @var Builder $query */
        $query = $modelClass::query()->whereIn((new $modelClass)->getTable().'.id', $ids);
        $query = $filterQuery($query);
        /** @var Collection<int, T> $models */
        $models = $query->get()->keyBy('id');

        return $rows
            ->map(fn (ContentRelation $row) => $models->get($row->source_id))
            ->filter()
            ->values();
    }

    /**
     * @param  list<int>  $excludeIds
     * @return Collection<int, Article>
     */
    private function termOverlapArticles(Article $article, int $limit, array $excludeIds): Collection
    {
        if ($limit <= 0) {
            return Article::query()->whereKey([])->get();
        }

        $termIds = $article->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return Article::query()->whereKey([])->get();
        }

        return Article::query()
            ->published()
            ->whereNotIn('id', $excludeIds)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  list<int>  $excludeIds
     * @return Collection<int, CaseStudy>
     */
    private function termOverlapCaseStudies(CaseStudy $study, int $limit, array $excludeIds): Collection
    {
        if ($limit <= 0) {
            return CaseStudy::query()->whereKey([])->get();
        }

        $termIds = $study->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return CaseStudy::query()->whereKey([])->get();
        }

        return CaseStudy::query()
            ->published()
            ->whereNotIn('id', $excludeIds)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  list<int>  $excludeIds
     * @return Collection<int, Product>
     */
    private function termOverlapProducts(Product $product, int $limit, array $excludeIds): Collection
    {
        if ($limit <= 0) {
            return Product::query()->whereKey([])->get();
        }

        $termIds = $product->terms()->pluck('terms.id');
        if ($termIds->isEmpty()) {
            return Product::query()->whereKey([])->get();
        }

        return Product::query()
            ->published()
            ->whereNotIn('id', $excludeIds)
            ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $termIds))
            ->orderByDesc('featured')
            ->orderByDesc('sort_order')
            ->limit($limit)
            ->get();
    }
}
