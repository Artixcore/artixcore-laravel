<?php

namespace App\Http\Controllers\Api\V1\Builder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Builder\AiBuilderProposeRequest;
use App\Models\Page;
use App\Services\Ai\Builder\AiBuilderOrchestrator;
use App\Support\Builder\BuilderDocumentValidator;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class BuilderAiController extends Controller
{
    public function __construct(
        private AiBuilderOrchestrator $orchestrator,
        private BuilderDocumentValidator $validator,
    ) {}

    public function propose(AiBuilderProposeRequest $request, Page $page): JsonResponse
    {
        $this->authorize('update', $page);

        $document = $request->validated('document');

        try {
            $this->validator->validate($document);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $result = $this->orchestrator->propose(
            $page,
            $document,
            $request->validated('prompt'),
            $request->validated('target_node_id'),
            $request->user()->id,
        );

        try {
            $this->validator->validate($result['document']);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => 'AI produced an invalid document: '.$e->getMessage(),
                'rationale' => $result['rationale'],
            ], 422);
        }

        return response()->json([
            'data' => [
                'rationale' => $result['rationale'],
                'document' => $result['document'],
                'provider_id' => $result['provider_id'],
                'prompt_tokens' => $result['prompt_tokens'],
                'completion_tokens' => $result['completion_tokens'],
            ],
        ]);
    }
}
