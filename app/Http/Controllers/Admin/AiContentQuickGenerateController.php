<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Support\AjaxRequestExpectations;
use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\CaseStudyGenerationService;
use App\Services\Ai\Exceptions\LlmTransportException;
use App\Services\Ai\MarketUpdateGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class AiContentQuickGenerateController extends Controller
{
    use RespondsWithAdminJson;

    public function article(Request $request, ArticleGenerationService $service): RedirectResponse|JsonResponse
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
            Log::warning('ai.quick_generate.article_failed', ['exception' => $e::class, 'message' => $e->getMessage()]);
            if (AjaxRequestExpectations::prefersJsonResponse($request)) {
                return $this->validationErrorResponse(
                    ['topic' => [__('The AI service could not complete this request. Please try again.')]],
                    __('Generation failed. Check your topic or API configuration.'),
                );
            }

            return redirect()->back()->withInput()->withErrors(['topic' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);
            if (AjaxRequestExpectations::prefersJsonResponse($request)) {
                return $this->errorResponse(__('Something went wrong. Please try again.'), 500);
            }

            return redirect()->back()->withInput()->withErrors(['topic' => __('Something went wrong. Please try again.')]);
        }

        $redirect = route('admin.articles.edit', $article);
        $message = __('Article draft generated — review before publishing.');

        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return $this->successResponse($message, ['article_id' => $article->id], $redirect);
        }

        return redirect()->to($redirect)->with('status', $message);
    }

    public function caseStudy(Request $request, CaseStudyGenerationService $service): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()?->can('ai_case_studies.generate'), 403);

        $topic = $request->validate([
            'topic' => ['nullable', 'string', 'max:2000'],
        ])['topic'] ?? null;

        try {
            $payload = $service->generateStructuredPayload($topic);
            $study = $service->createCaseStudyFromPayload($payload, $topic);
        } catch (LlmTransportException $e) {
            Log::warning('ai.quick_generate.case_study_failed', ['exception' => $e::class, 'message' => $e->getMessage()]);
            if (AjaxRequestExpectations::prefersJsonResponse($request)) {
                return $this->validationErrorResponse(
                    ['topic' => [__('The AI service could not complete this request. Please try again.')]],
                    __('Generation failed. Check your topic or API configuration.'),
                );
            }

            return redirect()->back()->withInput()->withErrors(['topic' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);
            if (AjaxRequestExpectations::prefersJsonResponse($request)) {
                return $this->errorResponse(__('Something went wrong. Please try again.'), 500);
            }

            return redirect()->back()->withInput()->withErrors(['topic' => __('Something went wrong. Please try again.')]);
        }

        $redirect = route('admin.case-studies.edit', $study);
        $message = __('Concept case study draft generated — review before publishing.');

        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return $this->successResponse($message, ['case_study_id' => $study->id], $redirect);
        }

        return redirect()->to($redirect)->with('status', $message);
    }

    public function marketUpdate(Request $request, MarketUpdateGenerationService $service): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()?->can('ai_market_updates.generate'), 403);

        $topic = $request->validate([
            'topic' => ['nullable', 'string', 'max:2000'],
        ])['topic'] ?? null;

        try {
            $payload = $service->generateStructuredPayload($topic);
            $row = $service->createMarketUpdateFromPayload($payload, $topic);
        } catch (LlmTransportException $e) {
            Log::warning('ai.quick_generate.market_update_failed', ['exception' => $e::class, 'message' => $e->getMessage()]);
            if (AjaxRequestExpectations::prefersJsonResponse($request)) {
                return $this->validationErrorResponse(
                    ['topic' => [__('The AI service could not complete this request. Please try again.')]],
                    __('Generation failed. Check your topic or API configuration.'),
                );
            }

            return redirect()->back()->withInput()->withErrors(['topic' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);
            if (AjaxRequestExpectations::prefersJsonResponse($request)) {
                return $this->errorResponse(__('Something went wrong. Please try again.'), 500);
            }

            return redirect()->back()->withInput()->withErrors(['topic' => __('Something went wrong. Please try again.')]);
        }

        $redirect = route('admin.market-updates.edit', $row);
        $message = __('Market update draft generated — fact-check before publishing.');

        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return $this->successResponse($message, ['market_update_id' => $row->id], $redirect);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
