<?php

namespace App\Http\Controllers\Api\V1\Builder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Builder\StoreSavedSectionRequest;
use App\Models\BuilderSavedSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuilderSavedSectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $uid = $request->user()->id;
        $rows = BuilderSavedSection::query()
            ->where(function ($q) use ($uid): void {
                $q->whereNull('user_id')->orWhere('user_id', $uid);
            })
            ->orderByDesc('id')
            ->limit(100)
            ->get(['id', 'name', 'document_json', 'user_id', 'created_at']);

        return response()->json(['data' => $rows]);
    }

    public function store(StoreSavedSectionRequest $request): JsonResponse
    {
        $section = BuilderSavedSection::query()->create([
            'user_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'document_json' => $request->validated('document'),
        ]);

        return response()->json(['data' => $section], 201);
    }

    public function destroy(Request $request, BuilderSavedSection $saved_section): JsonResponse
    {
        if ($saved_section->user_id !== null && $saved_section->user_id !== $request->user()->id) {
            abort(403);
        }

        $saved_section->delete();

        return response()->json(['data' => ['ok' => true]]);
    }
}
