<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('pages.services.index', [
            'services' => Service::query()->published()->orderBy('sort_order')->orderBy('title')->get(),
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
