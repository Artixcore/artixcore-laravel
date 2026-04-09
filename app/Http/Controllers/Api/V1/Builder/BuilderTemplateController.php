<?php

namespace App\Http\Controllers\Api\V1\Builder;

use App\Http\Controllers\Controller;
use App\Models\BuilderTemplate;
use App\Models\Page;
use App\Services\Audit\ActivityLogger;
use App\Services\Builder\PageBuilderStateService;
use App\Support\Builder\BuilderDocumentCloner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuilderTemplateController extends Controller
{
    public function __construct(
        private PageBuilderStateService $state,
        private BuilderDocumentCloner $cloner,
        private ActivityLogger $activityLogger,
    ) {}

    public function index(): JsonResponse
    {
        $rows = BuilderTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category', 'description']);

        return response()->json(['data' => $rows]);
    }

    public function apply(Request $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $slug = $request->validate(['slug' => ['required', 'string', 'max:128']])['slug'];

        $template = BuilderTemplate::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $raw = is_array($template->document_json) ? $template->document_json : [];
        $document = $this->cloner->cloneWithNewIds($raw);

        $version = $this->state->saveDraft(
            $page,
            $document,
            $request->user(),
            'template:'.$slug,
            null,
        );

        $this->activityLogger->log('builder.template_applied', $page, [
            'template_slug' => $slug,
            'version_id' => $version->id,
        ], $request);

        return response()->json([
            'data' => [
                'latest_version_id' => $version->id,
                'document' => $version->document_json,
            ],
        ]);
    }
}
