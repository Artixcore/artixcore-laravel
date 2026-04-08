<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('pages.portfolio.index', [
            'projects' => CaseStudy::query()->published()->orderByDesc('published_at')->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $project = CaseStudy::query()->published()->where('slug', $slug)->firstOrFail();

        return view('pages.portfolio.show', [
            'project' => $project,
        ]);
    }
}
