<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SiteResource;
use App\Models\SiteSetting;

class SiteController extends Controller
{
    public function show(): SiteResource
    {
        $settings = SiteSetting::instance()->load([
            'logoMedia',
            'faviconMedia',
            'ogDefaultMedia',
        ]);

        return new SiteResource($settings);
    }
}
