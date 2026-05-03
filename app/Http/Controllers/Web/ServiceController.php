<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\Content\RelatedContentService;
use App\Services\HtmlSanitizer;
use App\Support\MarketingContent;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
        private HtmlSanitizer $htmlSanitizer,
    ) {}

    public function index(): View
    {
        $settings = SiteSetting::safeInstance();
        $servicesPage = MarketingContent::mergeServicesPage($settings->services_page_content);

        $testimonials = collect();
        if (! empty($servicesPage['show_testimonials'])) {
            $testimonials = Testimonial::query()->published()->with('avatarMedia')->orderBy('sort_order')->orderBy('author_name')->get();
        }

        return view('pages.services.index', [
            'servicesPage' => $servicesPage,
            'services' => Service::query()->published()->with('featuredImageMedia')->orderBy('sort_order')->orderBy('title')->get(),
            'testimonials' => $testimonials,
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::query()
            ->published()
            ->where('slug', $slug)
            ->with(['featuredImageMedia', 'faqs', 'testimonials.avatarMedia'])
            ->firstOrFail();

        $bundle = $this->relatedContent->bundleForService($service);

        $body = $this->htmlSanitizer->sanitizeForPublic((string) ($service->body ?? ''));
        $benefits = is_array($service->benefits) ? $service->benefits : [];
        $process = is_array($service->process) ? $service->process : [];
        $technologies = is_array($service->technologies) ? $service->technologies : [];

        $faqs = $service->faqs()->published()->orderByPivot('sort_order')->get();
        $testimonials = $service->testimonials()->published()->with('avatarMedia')->orderByPivot('sort_order')->get();

        return view('pages.services.show', [
            'service' => $service,
            'bundle' => $bundle,
            'bodyHtml' => $this->htmlSanitizer->hardenLinks($body),
            'benefitsList' => $benefits,
            'processList' => $process,
            'technologiesList' => $technologies,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
        ]);
    }
}
