<?php

namespace App\Http\Controllers\Api\V1\Tools;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MicroToolResource;
use App\Models\MicroSavedReport;
use App\Models\MicroTool;
use App\Models\MicroToolFavorite;
use App\Models\MicroToolRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ToolMeController extends Controller
{
    public function favorites(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $ids = MicroToolFavorite::query()
            ->where('user_id', $user->id)
            ->pluck('micro_tool_id');

        $tools = MicroTool::query()->active()->whereIn('id', $ids)->orderBy('sort_order')->get();

        return MicroToolResource::collection($tools);
    }

    public function favoriteStore(Request $request, int $toolId): JsonResponse
    {
        $user = $request->user();
        $tool = MicroTool::query()->active()->whereKey($toolId)->firstOrFail();

        MicroToolFavorite::query()->firstOrCreate([
            'user_id' => $user->id,
            'micro_tool_id' => $tool->id,
        ]);

        return response()->json(['data' => ['ok' => true]]);
    }

    public function favoriteDestroy(Request $request, int $toolId): JsonResponse
    {
        $user = $request->user();
        MicroToolFavorite::query()
            ->where('user_id', $user->id)
            ->where('micro_tool_id', $toolId)
            ->delete();

        return response()->json(['data' => ['ok' => true]]);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $runs = MicroToolRun::query()
            ->where('user_id', $user->id)
            ->with(['tool', 'result'])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $data = $runs->map(function (MicroToolRun $run): array {
            return [
                'id' => $run->id,
                'status' => $run->status,
                'created_at' => $run->created_at?->toIso8601String(),
                'tool' => $run->tool ? [
                    'slug' => $run->tool->slug,
                    'title' => $run->tool->title,
                    'category' => $run->tool->category,
                ] : null,
                'input_summary' => $run->input_summary,
                'result' => $run->result?->payload,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function reports(Request $request): JsonResponse
    {
        $user = $request->user();
        $reports = MicroSavedReport::query()
            ->where('user_id', $user->id)
            ->with(['tool', 'run.result'])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $data = $reports->map(function (MicroSavedReport $r): array {
            return [
                'id' => $r->id,
                'title' => $r->title,
                'created_at' => $r->created_at?->toIso8601String(),
                'tool' => [
                    'slug' => $r->tool->slug,
                    'title' => $r->tool->title,
                ],
                'run_id' => $r->micro_tool_run_id,
                'result' => $r->run?->result?->payload,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function reportShow(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $report = MicroSavedReport::query()
            ->where('user_id', $user->id)
            ->whereKey($id)
            ->with(['tool', 'run.result'])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $report->id,
                'title' => $report->title,
                'created_at' => $report->created_at?->toIso8601String(),
                'tool' => [
                    'slug' => $report->tool->slug,
                    'title' => $report->tool->title,
                ],
                'run_id' => $report->micro_tool_run_id,
                'result' => $report->run?->result?->payload,
            ],
        ]);
    }

    public function reportStore(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'micro_tool_run_id' => 'required|integer|exists:micro_tool_runs,id',
            'title' => 'required|string|max:200',
        ]);

        $run = MicroToolRun::query()
            ->where('user_id', $user->id)
            ->whereKey($validated['micro_tool_run_id'])
            ->firstOrFail();

        $report = MicroSavedReport::query()->create([
            'user_id' => $user->id,
            'micro_tool_id' => $run->micro_tool_id,
            'micro_tool_run_id' => $run->id,
            'title' => $validated['title'],
        ]);

        return response()->json([
            'data' => ['id' => $report->id],
        ], 201);
    }
}
