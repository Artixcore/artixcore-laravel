<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Support\MarketingContent;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::instance();
        $home = MarketingContent::mergeHomepage($settings->homepage_content);

        return view('pages.home.index', [
            'home' => $home,
            'services' => Service::query()->published()->orderBy('sort_order')->orderBy('title')->get(),
            'projects' => CaseStudy::query()->published()->where('featured', true)->orderByDesc('published_at')->take(6)->get(),
            'articles' => Article::query()->published()->orderByDesc('published_at')->take(3)->get(),
            'testimonials' => Testimonial::query()->published()->orderBy('sort_order')->orderBy('author_name')->get(),
        ]);
    }
}
