<?php

namespace App\Services;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Resolves homepage sections and linked content for public rendering.
 * Drops unpublished or missing records; never throws for bad data.
 */
class HomepageContentResolver
{
    public const ITEM_TYPES = [
        'service' => Service::class,
        'product' => Product::class,
        'portfolio_item' => PortfolioItem::class,
        'case_study' => CaseStudy::class,
        'article' => Article::class,
        'testimonial' => Testimonial::class,
        'faq' => Faq::class,
    ];

    /** @var array<string, string> section key => Blade partial name under home/sections/ */
    public const SECTION_PARTIALS = [
        'hero' => 'hero',
        'trust_metrics' => 'trust-metrics',
        'partner_logos' => 'partner-logos',
        'about' => 'about',
        'featured_services' => 'featured-services',
        'featured_platforms' => 'featured-platforms',
        'featured_portfolio' => 'featured-work',
        'featured_case_studies' => 'case-studies',
        'latest_articles' => 'latest-articles',
        'testimonials' => 'testimonials',
        'faq' => 'faqs',
        'final_cta' => 'final-cta',
    ];

    /**
     * @return array{seo: array<string, mixed>, sections: Collection<int, array<string, mixed>>}
     */
    public function resolveForPublic(SiteSetting $site): array
    {
        $seo = $this->mergeHomepageSeo($site);

        $sections = collect();
        try {
            if (! Schema::hasTable('homepage_sections')) {
                return ['seo' => $seo, 'sections' => $sections];
            }

            $rows = HomepageSection::query()
                ->where('is_enabled', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->with(['items' => function ($q): void {
                    $q->where('is_enabled', true)->orderBy('sort_order')->orderBy('id');
                }])
                ->get();

            foreach ($rows as $row) {
                $block = $this->resolveSection($row);
                if ($block !== null) {
                    $sections->push($block);
                }
            }
        } catch (\Throwable) {
            // keep empty sections; controller may fall back to legacy
        }

        return ['seo' => $seo, 'sections' => $sections];
    }

    public function hasManagedSections(): bool
    {
        try {
            if (! Schema::hasTable('homepage_sections')) {
                return false;
            }

            return HomepageSection::query()->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function mergeHomepageSeo(SiteSetting $site): array
    {
        $defaults = [
            'meta_title' => (string) config('marketing.homepage.meta_title', ''),
            'meta_description' => (string) config('marketing.homepage.meta_description', ''),
            'meta_keywords' => (string) config('marketing.default_keywords', ''),
            'canonical_url' => url('/'),
            'robots' => 'index, follow',
            'og_title' => (string) config('marketing.homepage.og_title', config('marketing.homepage.meta_title', '')),
            'og_description' => (string) config('marketing.homepage.og_description', config('marketing.homepage.meta_description', '')),
            'og_image' => null,
            'twitter_title' => null,
            'twitter_description' => null,
            'twitter_image' => null,
        ];

        $stored = $site->homepage_seo;
        if (! is_array($stored)) {
            $stored = [];
        }

        $merged = array_merge($defaults, $stored);

        foreach (['meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots', 'og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description', 'twitter_image'] as $k) {
            if (! array_key_exists($k, $merged)) {
                $merged[$k] = $defaults[$k] ?? null;
            }
        }

        if ($merged['twitter_title'] === null || $merged['twitter_title'] === '') {
            $merged['twitter_title'] = $merged['og_title'] ?: $merged['meta_title'];
        }
        if ($merged['twitter_description'] === null || $merged['twitter_description'] === '') {
            $merged['twitter_description'] = $merged['og_description'] ?: $merged['meta_description'];
        }
        if ($merged['twitter_image'] === null || $merged['twitter_image'] === '') {
            $merged['twitter_image'] = $merged['og_image'];
        }

        $defaultOg = config('articles.fallback_image_url');
        if (($merged['og_image'] === null || $merged['og_image'] === '') && is_string($defaultOg) && $defaultOg !== '') {
            $merged['og_image'] = $defaultOg;
        }

        return $merged;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function resolveSection(HomepageSection $section): ?array
    {
        $key = (string) $section->key;
        $partial = self::SECTION_PARTIALS[$key] ?? null;
        if ($partial === null) {
            return null;
        }

        $settings = is_array($section->settings) ? $section->settings : [];
        $items = $this->resolveItems($section, $key, $settings);
        $imageUrl = $this->publicImageUrl($section->image_path);

        return [
            'key' => $key,
            'partial' => $partial,
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'badge_text' => $section->badge_text,
            'button_text' => $section->button_text,
            'button_url' => $section->button_url,
            'secondary_button_text' => $section->secondary_button_text,
            'secondary_button_url' => $section->secondary_button_url,
            'image_path' => $section->image_path,
            'image_url' => $imageUrl,
            'settings' => $settings,
            'items' => $items,
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<array<string, mixed>>
     */
    private function resolveItems(HomepageSection $section, string $sectionKey, array $settings): array
    {
        $rows = $section->items ?? collect();
        $out = [];

        if ($rows->isNotEmpty()) {
            $byType = [];
            foreach ($rows as $row) {
                if (! $row instanceof HomepageSectionItem) {
                    continue;
                }
                $t = (string) ($row->item_type ?? '');
                if ($t === '' || $row->item_id === null) {
                    continue;
                }
                if (! isset(self::ITEM_TYPES[$t])) {
                    continue;
                }
                $byType[$t] ??= [];
                $byType[$t][] = (int) $row->item_id;
            }

            $loaded = [];
            foreach ($byType as $type => $ids) {
                $ids = array_values(array_unique(array_filter($ids)));
                if ($ids === []) {
                    continue;
                }
                $class = self::ITEM_TYPES[$type];
                $q = $class::query()->whereIn('id', $ids);
                $q = $this->applyPublishedScope($type, $q);
                /** @var EloquentCollection<int, Model> $models */
                $models = $q->get();
                foreach ($models as $m) {
                    $loaded[$type][(int) $m->getKey()] = $m;
                }
            }

            foreach ($rows as $row) {
                if (! $row instanceof HomepageSectionItem) {
                    continue;
                }
                $t = (string) ($row->item_type ?? '');
                if ($t === '' || $row->item_id === null) {
                    continue;
                }
                $id = (int) $row->item_id;
                $model = $loaded[$t][$id] ?? null;
                if ($model === null) {
                    continue;
                }
                $presented = $this->presentItem($t, $model, $row);
                if ($presented !== null) {
                    $out[] = $presented;
                }
            }
        }

        return $this->applySectionFallbacksInner($sectionKey, $settings, $out);
    }

    /**
     * @param  list<array<string, mixed>>  $out
     * @return list<array<string, mixed>>
     */
    private function applySectionFallbacksInner(string $sectionKey, array $settings, array $out): array
    {
        if ($out !== []) {
            return $out;
        }

        try {
            if ($sectionKey === 'latest_articles') {
                $limit = (int) ($settings['auto_limit'] ?? 3);
                if ($limit < 1) {
                    $limit = 3;
                }
                if (! Schema::hasTable('articles')) {
                    return [];
                }
                $articles = Article::query()
                    ->published()
                    ->orderByDesc('published_at')
                    ->limit($limit)
                    ->get();
                foreach ($articles as $article) {
                    $p = $this->presentItem('article', $article, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }

            if ($sectionKey === 'featured_services') {
                $limit = (int) ($settings['fallback_limit'] ?? 6);
                if ($limit < 1) {
                    $limit = 6;
                }
                if (! Schema::hasTable('services')) {
                    return [];
                }
                foreach (Service::query()->published()->orderBy('sort_order')->orderBy('title')->limit($limit)->get() as $service) {
                    $p = $this->presentItem('service', $service, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }

            if ($sectionKey === 'featured_case_studies') {
                $limit = (int) ($settings['fallback_limit'] ?? 6);
                if ($limit < 1) {
                    $limit = 6;
                }
                if (! Schema::hasTable('case_studies')) {
                    return [];
                }
                $q = CaseStudy::query()->published();
                if (Schema::hasColumn('case_studies', 'featured')) {
                    $q->where('featured', true);
                }
                foreach ($q->orderByDesc('published_at')->limit($limit)->get() as $cs) {
                    $p = $this->presentItem('case_study', $cs, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }

            if ($sectionKey === 'featured_platforms') {
                $limit = (int) ($settings['fallback_limit'] ?? 6);
                if ($limit < 1) {
                    $limit = 6;
                }
                if (! Schema::hasTable('products')) {
                    return [];
                }
                $q = Product::query()->published();
                if (Schema::hasColumn('products', 'featured')) {
                    $q->where('featured', true);
                }
                foreach ($q->orderBy('sort_order')->orderBy('title')->limit($limit)->get() as $product) {
                    $p = $this->presentItem('product', $product, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }

            if ($sectionKey === 'featured_portfolio') {
                $limit = (int) ($settings['fallback_limit'] ?? 6);
                if ($limit < 1) {
                    $limit = 6;
                }
                if (! Schema::hasTable('portfolio_items')) {
                    return [];
                }
                $q = PortfolioItem::query()->published();
                if (Schema::hasColumn('portfolio_items', 'featured')) {
                    $q->where('featured', true);
                }
                foreach ($q->orderBy('sort_order')->orderByDesc('published_at')->limit($limit)->get() as $pi) {
                    $p = $this->presentItem('portfolio_item', $pi, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }

            if ($sectionKey === 'testimonials') {
                $limit = (int) ($settings['fallback_limit'] ?? 12);
                if (! Schema::hasTable('testimonials')) {
                    return [];
                }
                foreach (Testimonial::query()->published()->orderBy('sort_order')->orderBy('author_name')->limit($limit)->get() as $t) {
                    $p = $this->presentItem('testimonial', $t, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }

            if ($sectionKey === 'faq') {
                $limit = (int) ($settings['fallback_limit'] ?? 8);
                if (! Schema::hasTable('faqs')) {
                    return [];
                }
                foreach (Faq::query()->published()->orderBy('sort_order')->orderBy('id')->limit($limit)->get() as $faq) {
                    $p = $this->presentItem('faq', $faq, null);
                    if ($p !== null) {
                        $out[] = $p;
                    }
                }
            }
        } catch (\Throwable) {
            return $out;
        }

        return $out;
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    private function applyPublishedScope(string $type, Builder $query): Builder
    {
        return match ($type) {
            'service' => $query->published(),
            'product' => $query->published(),
            'portfolio_item' => $query->published(),
            'case_study' => $query->published(),
            'article' => $query->published(),
            'testimonial' => $query->published(),
            'faq' => $query->published(),
            default => $query,
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    private function presentItem(string $type, Model $model, ?HomepageSectionItem $link): ?array
    {
        $overrideTitle = $link?->title_override;
        $overrideDesc = $link?->description_override;
        $overrideImage = $link?->image_override;
        $btnText = $link?->button_text;
        $btnUrl = $link?->button_url;

        try {
            return match ($type) {
                'service' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'title' => (string) ($overrideTitle ?: $model->title),
                    'summary' => (string) ($overrideDesc ?: ($model->summary ?? '')),
                    'icon' => (string) ($model->icon ?: 'bi bi-stack'),
                    'url' => $btnUrl ?: route('services.show', $model->slug),
                    'button_text' => $btnText,
                    'image_url' => $this->publicImageUrl($overrideImage) ?? ($model->main_image_url ?? null),
                ],
                'product' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'title' => (string) ($overrideTitle ?: $model->title),
                    'summary' => (string) ($overrideDesc ?: ($model->tagline ?? $model->summary ?? '')),
                    'url' => $btnUrl ?: route('saas-platforms.show', $model->slug),
                    'button_text' => $btnText,
                    'image_url' => $this->publicImageUrl($overrideImage) ?? ($model->main_image_url ?? null),
                ],
                'portfolio_item' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'title' => (string) ($overrideTitle ?: $model->title),
                    'summary' => (string) ($overrideDesc ?: ($model->short_description ?? '')),
                    'url' => $btnUrl ?: route('portfolio.show', $model->slug),
                    'button_text' => $btnText,
                    'image_url' => $this->publicImageUrl($overrideImage) ?? ($model->main_image_url ?? null),
                ],
                'case_study' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'title' => (string) ($overrideTitle ?: $model->title),
                    'summary' => (string) ($overrideDesc ?: ($model->summary ?? '')),
                    'client' => (string) ($model->client_display_name ?? $model->client_name ?? ''),
                    'url' => $btnUrl ?: route('case-studies.show', $model->slug),
                    'button_text' => $btnText,
                    'image_url' => $this->publicImageUrl($overrideImage) ?: ($model->main_image_url ?? null),
                ],
                'article' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'title' => (string) ($overrideTitle ?: $model->title),
                    'summary' => (string) ($overrideDesc ?: ($model->summary ?? '')),
                    'url' => $btnUrl ?: route('articles.show', $model->slug),
                    'button_text' => $btnText,
                    'published_at' => $model->published_at,
                    'image_url' => $this->publicImageUrl($overrideImage) ?: ($model->main_image_url ?? null),
                ],
                'testimonial' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'author' => (string) ($overrideTitle ?: $model->author_name),
                    'role' => (string) ($model->role ?? ''),
                    'company' => (string) ($model->company ?? ''),
                    'body' => (string) ($overrideDesc ?: $model->body),
                    'rating' => (int) ($model->rating ?? 0),
                    'url' => $btnUrl ?: '',
                    'button_text' => $btnText,
                    'image_url' => $this->publicImageUrl($overrideImage) ?? $this->testimonialAvatar($model),
                ],
                'faq' => [
                    'type' => $type,
                    'id' => (int) $model->getKey(),
                    'question' => (string) ($overrideTitle ?: $model->question),
                    'answer' => (string) ($overrideDesc ?: ($model->answer ?? '')),
                    'url' => $btnUrl ?: route('faq'),
                    'button_text' => $btnText,
                ],
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    private function testimonialAvatar(Testimonial $t): ?string
    {
        try {
            $url = $t->avatarMedia?->absoluteUrl();
            if (is_string($url) && $url !== '') {
                return $url;
            }
        } catch (\Throwable) {
        }

        return null;
    }

    public function publicImageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        return asset($path);
    }
}
