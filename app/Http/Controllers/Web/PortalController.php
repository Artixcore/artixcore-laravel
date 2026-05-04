<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Lead;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        if ($user === null) {
            abort(403);
        }

        $myLeads = collect();
        if (Schema::hasTable('leads')) {
            try {
                $myLeads = Lead::query()
                    ->where('email', $user->email)
                    ->orderByDesc('id')
                    ->limit(20)
                    ->get();
            } catch (\Throwable) {
                $myLeads = collect();
            }
        }

        $recommendedServices = collect();
        if (Schema::hasTable('services')) {
            try {
                $recommendedServices = Service::query()
                    ->published()
                    ->orderByDesc('featured')
                    ->orderBy('sort_order')
                    ->limit(6)
                    ->get();
            } catch (\Throwable) {
                $recommendedServices = collect();
            }
        }

        $latestArticles = collect();
        if (Schema::hasTable('articles')) {
            try {
                $latestArticles = Article::query()
                    ->published()
                    ->orderByDesc('published_at')
                    ->limit(5)
                    ->get();
            } catch (\Throwable) {
                $latestArticles = collect();
            }
        }

        return view('portal.index', [
            'myLeads' => $myLeads,
            'recommendedServices' => $recommendedServices,
            'latestArticles' => $latestArticles,
        ]);
    }
}
