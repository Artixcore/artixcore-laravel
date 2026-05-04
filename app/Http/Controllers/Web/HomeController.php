<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\HomepageContentResolver;
use App\Support\MarketingContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::safeInstance();
        $resolver = app(HomepageContentResolver::class);
        $homepageSeo = $resolver->mergeHomepageSeo($settings);

        try {
            if ($resolver->hasManagedSections()) {
                $resolved = $resolver->resolveForPublic($settings);

                return view('pages.home.index', [
                    'legacyHome' => false,
                    'homepageSeo' => $resolved['seo'],
                    'homepageSections' => $resolved['sections'],
                ]);
            }
        } catch (\Throwable) {
            //
        }

        $home = MarketingContent::mergeHomepage($settings->homepage_content ?? null);

        return view('pages.home.index', [
            'legacyHome' => true,
            'homepageSeo' => $homepageSeo,
            'home' => $home,
            'services' => $this->safePublishedServices(),
            'projects' => $this->safeFeaturedCaseStudies(),
            'articles' => $this->safeLatestArticles(),
            'testimonials' => $this->safePublishedTestimonials(),
        ]);
    }

    /**
     * @return Collection<int, Service>
     */
    private function safePublishedServices(): Collection
    {
        try {
            if (! Schema::hasTable('services')) {
                return collect();
            }

            return Service::query()->published()->orderBy('sort_order')->orderBy('title')->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @return Collection<int, CaseStudy>
     */
    private function safeFeaturedCaseStudies(): Collection
    {
        try {
            if (! Schema::hasTable('case_studies')) {
                return collect();
            }

            return CaseStudy::query()->published()->where('featured', true)->orderByDesc('published_at')->take(6)->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @return Collection<int, Article>
     */
    private function safeLatestArticles(): Collection
    {
        try {
            if (! Schema::hasTable('articles')) {
                return collect();
            }

            return Article::query()->published()->orderByDesc('published_at')->take(3)->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @return Collection<int, Testimonial>
     */
    private function safePublishedTestimonials(): Collection
    {
        try {
            if (! Schema::hasTable('testimonials')) {
                return collect();
            }

            return Testimonial::query()->published()->orderBy('sort_order')->orderBy('author_name')->get();
        } catch (\Throwable) {
            return collect();
        }
    }
}
