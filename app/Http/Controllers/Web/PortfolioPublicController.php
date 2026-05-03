<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use App\Services\Content\RelatedContentService;
use App\Services\HtmlSanitizer;
use Illuminate\View\View;

class PortfolioPublicController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
        private HtmlSanitizer $htmlSanitizer,
    ) {}

    public function index(): View
    {
        $items = PortfolioItem::query()
            ->published()
            ->with('mainImageMedia')
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('pages.portfolio.index', [
            'portfolioItems' => $items,
        ]);
    }

    public function show(string $slug): View
    {
        $item = PortfolioItem::query()
            ->published()
            ->where('slug', $slug)
            ->with(['mainImageMedia', 'faqs', 'testimonials.avatarMedia'])
            ->firstOrFail();

        $this->authorize('view', $item);

        $body = $this->htmlSanitizer->sanitizeForPublic((string) ($item->body ?? ''));
        $challenge = $this->htmlSanitizer->sanitizeForPublic((string) ($item->challenge ?? ''));
        $solution = $this->htmlSanitizer->sanitizeForPublic((string) ($item->solution ?? ''));

        $bundle = $this->relatedContent->bundleForPortfolioItem($item);

        $faqs = $item->faqs()->published()->orderByPivot('sort_order')->get();
        $testimonials = $item->testimonials()->published()->with('avatarMedia')->orderByPivot('sort_order')->get();

        return view('pages.portfolio.show', [
            'item' => $item,
            'bodyHtml' => $this->htmlSanitizer->hardenLinks($body),
            'challengeHtml' => $this->htmlSanitizer->hardenLinks($challenge),
            'solutionHtml' => $this->htmlSanitizer->hardenLinks($solution),
            'bundle' => $bundle,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
            'videoEmbed' => $item->video_embed,
        ]);
    }
}
