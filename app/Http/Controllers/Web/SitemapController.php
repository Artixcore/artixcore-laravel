<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\MarketUpdate;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            route('home'),
            route('about'),
            route('services.index'),
            route('saas-platforms'),
            route('portfolio.index'),
            route('case-studies.index'),
            route('market-updates.index'),
            route('articles.index'),
            route('blog.index'),
            route('lead.create'),
            route('get-started'),
            route('careers'),
            route('faq'),
            route('privacy'),
            route('terms'),
        ]);

        if (Schema::hasTable('services')) {
            foreach (Service::query()->published()->orderBy('sort_order')->get(['slug']) as $service) {
                $urls->push(route('services.show', $service->slug));
            }
        }

        if (Schema::hasTable('articles')) {
            foreach (Article::query()->published()->orderByDesc('published_at')->limit(500)->get(['slug']) as $article) {
                $urls->push(route('articles.show', $article->slug));
            }
        }

        if (Schema::hasTable('case_studies')) {
            foreach (CaseStudy::query()->published()->orderByDesc('published_at')->limit(500)->get(['slug']) as $caseStudy) {
                $urls->push(route('case-studies.show', $caseStudy->slug));
            }
        }

        if (Schema::hasTable('products')) {
            foreach (Product::query()->published()->orderBy('sort_order')->get(['slug']) as $product) {
                $urls->push(route('saas-platforms.show', $product->slug));
            }
        }

        if (Schema::hasTable('market_updates')) {
            foreach (MarketUpdate::query()->published()->orderByDesc('published_at')->limit(300)->get(['slug']) as $update) {
                $urls->push(route('market-updates.show', $update->slug));
            }
        }

        $unique = $urls->unique()->values();
        $lastmod = now()->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($unique as $loc) {
            $xml .= '<url>'
                .'<loc>'.e($loc).'</loc>'
                .'<lastmod>'.e($lastmod).'</lastmod>'
                .'<changefreq>weekly</changefreq>'
                .'<priority>0.8</priority>'
                .'</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
