<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Support\MarketingContent;
use Illuminate\View\View;

class ServiceController extends Controller
{
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
        $service = Service::query()->published()->where('slug', $slug)->firstOrFail();

        return view('pages.services.show', [
            'service' => $service,
        ]);
    }
}
