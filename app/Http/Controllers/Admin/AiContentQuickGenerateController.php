<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\CaseStudyGenerationService;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Ai\MarketUpdateGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AiContentQuickGenerateController extends Controller
{
    public function article(Request $request, ArticleGenerationService $service): RedirectResponse
    {
        abort_unless($request->user()?->can('ai_articles.generate'), 403);

        $data = $request->validate([
            'article_type' => ['required', 'string', Rule::in([
                'latest_discovery',
                'today_trends',
                'latest_tech',
                'company_update',
                'tutorial',
                'insight',
            ])],
            'topic' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $payload = $service->generateStructuredPayload($data['article_type'], $data['topic'] ?? null);
            $article = $service->createArticleFromPayload($payload, $data['article_type'], $data['topic'] ?? null);
        } catch (LlmTransportException $e) {
            return redirect()->back()->withInput()->withErrors(['topic' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('status', 'Article draft generated — review before publishing.');
    }

    public function caseStudy(Request $request, CaseStudyGenerationService $service): RedirectResponse
    {
        abort_unless($request->user()?->can('ai_case_studies.generate'), 403);

        $topic = $request->validate([
            'topic' => ['nullable', 'string', 'max:2000'],
        ])['topic'] ?? null;

        try {
            $payload = $service->generateStructuredPayload($topic);
            $study = $service->createCaseStudyFromPayload($payload, $topic);
        } catch (LlmTransportException $e) {
            return redirect()->back()->withInput()->withErrors(['topic' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.case-studies.edit', $study)
            ->with('status', 'Concept case study draft generated — review before publishing.');
    }

    public function marketUpdate(Request $request, MarketUpdateGenerationService $service): RedirectResponse
    {
        abort_unless($request->user()?->can('ai_market_updates.generate'), 403);

        $topic = $request->validate([
            'topic' => ['nullable', 'string', 'max:2000'],
        ])['topic'] ?? null;

        try {
            $payload = $service->generateStructuredPayload($topic);
            $row = $service->createMarketUpdateFromPayload($payload, $topic);
        } catch (LlmTransportException $e) {
            return redirect()->back()->withInput()->withErrors(['topic' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.market-updates.edit', $row)
            ->with('status', 'Market update draft generated — fact-check before publishing.');
    }
}
