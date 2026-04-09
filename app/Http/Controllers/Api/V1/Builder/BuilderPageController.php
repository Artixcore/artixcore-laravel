<?php

namespace App\Http\Controllers\Api\V1\Builder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Builder\PublishBuilderPageRequest;
use App\Http\Requests\Builder\UpdateBuilderDocumentRequest;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\Audit\ActivityLogger;
use App\Services\Builder\PageBuilderStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BuilderPageController extends Controller
{
    public function __construct(
        private PageBuilderStateService $state,
        private ActivityLogger $activityLogger,
    ) {}

    public function show(Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $loaded = $this->state->load($page);

        return response()->json([
            'data' => [
                'page' => [
                    'id' => $loaded['page']->id,
                    'path' => $loaded['page']->path,
                    'title' => $loaded['page']->title,
                    'status' => $loaded['page']->status,
                    'meta_title' => $loaded['page']->meta_title,
                    'meta_description' => $loaded['page']->meta_description,
                    'published_at' => $loaded['page']->published_at?->toIso8601String(),
                    'scheduled_publish_at' => $loaded['page']->scheduled_publish_at?->toIso8601String(),
                    'archived_at' => $loaded['page']->archived_at?->toIso8601String(),
                ],
                'document' => $loaded['document'],
                'latest_version_id' => $loaded['latest_version_id'],
            ],
        ]);
    }

    public function updateDocument(UpdateBuilderDocumentRequest $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        try {
            $version = $this->state->saveDraft(
                $page,
                $request->validated('document'),
                $request->user(),
                $request->input('label', 'autosave'),
                $request->validated('base_version_id'),
            );
        } catch (InvalidArgumentException $e) {
            if ($e->getMessage() === 'conflict') {
                $latest = $page->versions()->orderByDesc('id')->first();

                return response()->json([
                    'message' => 'A newer version exists. Reload and merge.',
                    'latest_version_id' => $latest?->id,
                ], 409);
            }
            throw $e;
        }

        $this->activityLogger->log('builder.document_saved', $page, [
            'version_id' => $version->id,
        ], $request);

        return response()->json([
            'data' => [
                'latest_version_id' => $version->id,
            ],
        ]);
    }

    public function versions(Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $rows = $page->versions()
            ->orderByDesc('id')
            ->limit(50)
            ->get(['id', 'label', 'user_id', 'created_at']);

        return response()->json([
            'data' => $rows->map(static fn (PageVersion $v): array => [
                'id' => $v->id,
                'label' => $v->label,
                'user_id' => $v->user_id,
                'created_at' => $v->created_at->toIso8601String(),
            ]),
        ]);
    }

    public function restoreVersion(Request $request, Page $page, PageVersion $version): JsonResponse
    {
        $this->authorize('update', $page);

        $newVersion = $this->state->restoreVersion($page, $version, $request->user());
        $this->activityLogger->log('builder.version_restored', $page, [
            'from_version_id' => $version->id,
            'new_version_id' => $newVersion->id,
        ], $request);

        return response()->json([
            'data' => [
                'latest_version_id' => $newVersion->id,
                'document' => $newVersion->document_json,
            ],
        ]);
    }

    public function publish(PublishBuilderPageRequest $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $scheduled = $request->validated('scheduled_at');
        $dt = $scheduled !== null ? new \DateTimeImmutable($scheduled) : null;

        $this->state->publish($page, $dt);
        $this->activityLogger->log('builder.page_published', $page, [
            'scheduled' => $dt !== null,
        ], $request);

        return response()->json([
            'data' => ['ok' => true, 'page' => $page->fresh()->only([
                'id', 'status', 'published_at', 'scheduled_publish_at',
            ])],
        ]);
    }

    public function archive(Request $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $this->state->archive($page);
        $this->activityLogger->log('builder.page_archived', $page, [], $request);

        return response()->json(['data' => ['ok' => true]]);
    }

    public function unpublish(Request $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $this->state->unpublish($page);
        $this->activityLogger->log('builder.page_unpublished', $page, [], $request);

        return response()->json(['data' => ['ok' => true]]);
    }

    public function export(Page $page): JsonResponse
    {
        $this->authorize('update', $page);
        $loaded = $this->state->load($page);

        return response()->json([
            'data' => [
                'schemaVersion' => $loaded['document']['schemaVersion'] ?? 1,
                'exported_at' => now()->toIso8601String(),
                'page' => [
                    'path' => $loaded['page']->path,
                    'title' => $loaded['page']->title,
                ],
                'document' => $loaded['document'],
            ],
        ]);
    }

    public function import(UpdateBuilderDocumentRequest $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $version = $this->state->saveDraft(
            $page,
            $request->validated('document'),
            $request->user(),
            'import',
            null,
        );

        $this->activityLogger->log('builder.document_imported', $page, [
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
