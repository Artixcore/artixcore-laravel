<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\MarketingContent;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function show(): View
    {
        $settings = SiteSetting::instance();
        $about = MarketingContent::mergeAbout($settings->about_content);

        return view('pages.about', [
            'about' => $about,
        ]);
    }
}
