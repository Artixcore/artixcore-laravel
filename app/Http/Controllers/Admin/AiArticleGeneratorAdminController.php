<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\Exceptions\LlmTransportException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AiArticleGeneratorAdminController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->can('ai_articles.generate'), 403);

        return view('admin.ai-article-generator');
    }

    public function store(Request $request, ArticleGenerationService $service): RedirectResponse
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
            ->with('status', 'Draft generated with Ali 1.0 — review before publishing.');
    }
}
