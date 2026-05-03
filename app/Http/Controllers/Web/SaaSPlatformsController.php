<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\Content\RelatedContentService;
use App\Services\HtmlSanitizer;
use App\Support\MarketingContent;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SaaSPlatformsController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
        private HtmlSanitizer $htmlSanitizer,
    ) {}

    public function index(): View
    {
        $settings = SiteSetting::safeInstance();
        $saasPage = MarketingContent::mergeSaaSPage($settings->saas_page_content);

        $platforms = Product::query()
            ->published()
            ->with('mainImageMedia')
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $testimonials = collect();
        if (! empty($saasPage['show_testimonials'])) {
            $testimonials = Testimonial::query()->published()->with('avatarMedia')->orderBy('sort_order')->orderBy('author_name')->get();
        }

        $faqs = collect();
        if (! empty($saasPage['show_faq'])) {
            $faqs = Faq::query()
                ->published()
                ->where('show_on_saas_page', true)
                ->orderBy('sort_order')
                ->get();
        }

        $caseStudies = collect();
        if (! empty($saasPage['show_case_studies'])) {
            $caseStudies = $this->resolveCaseStudies($saasPage);
        }

        $highlightedServices = $this->resolveHighlightedServices($saasPage);

        return view('pages.saas-platforms.index', [
            'saasPage' => $saasPage,
            'platforms' => $platforms,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'caseStudies' => $caseStudies,
            'highlightedServices' => $highlightedServices,
        ]);
    }

    public function show(string $slug): View
    {
        $platform = Product::query()
            ->published()
            ->where('slug', $slug)
            ->with(['terms.taxonomy', 'mainImageMedia', 'faqs', 'testimonials.avatarMedia'])
            ->firstOrFail();

        $this->authorize('view', $platform);

        $bundle = $this->relatedContent->bundleForProduct($platform);

        $body = $this->htmlSanitizer->sanitizeForPublic((string) ($platform->body ?? ''));
        $features = is_array($platform->features) ? $platform->features : [];
        $useCases = is_array($platform->use_cases) ? $platform->use_cases : [];

        $faqs = $platform->faqs()->published()->orderByPivot('sort_order')->get();
        $testimonials = $platform->testimonials()->published()->with('avatarMedia')->orderByPivot('sort_order')->get();

        return view('pages.saas-platforms.show', [
            'platform' => $platform,
            'bundle' => $bundle,
            'bodyHtml' => $this->htmlSanitizer->hardenLinks($body),
            'featuresList' => $features,
            'useCasesList' => $useCases,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
            'videoEmbed' => $platform->video_embed,
        ]);
    }

    /**
     * @param  array<string, mixed>  $saasPage
     * @return Collection<int, CaseStudy>
     */
    private function resolveCaseStudies(array $saasPage): Collection
    {
        $limit = (int) ($saasPage['case_study_limit'] ?? 3);
        $limit = max(1, min($limit, 12));

        $slugs = $saasPage['case_study_slugs'] ?? [];
        if (is_array($slugs) && count($slugs) > 0) {
            $slugs = array_values(array_filter($slugs, fn ($s): bool => is_string($s) && $s !== ''));

            $cases = CaseStudy::query()
                ->published()
                ->whereIn('slug', $slugs)
                ->get()
                ->sortBy(function (CaseStudy $c) use ($slugs): int {
                    $i = array_search($c->slug, $slugs, true);

                    return $i !== false ? $i : 9999;
                })
                ->values();

            return $cases->take($limit);
        }

        return CaseStudy::query()
            ->published()
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->take($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $saasPage
     * @return Collection<int, Service>
     */
    private function resolveHighlightedServices(array $saasPage): Collection
    {
        $slugs = $saasPage['service_slugs'] ?? [];
        if (! is_array($slugs) || count($slugs) === 0) {
            return collect();
        }

        $slugs = array_values(array_filter($slugs, fn ($s): bool => is_string($s) && $s !== ''));

        $services = Service::query()
            ->published()
            ->whereIn('slug', $slugs)
            ->with('featuredImageMedia')
            ->get();

        return $services
            ->sortBy(function (Service $s) use ($slugs): int {
                $i = array_search($s->slug, $slugs, true);

                return $i !== false ? $i : 9999;
            })
            ->values();
    }
}
