<?php

namespace App\Http\Controllers\Api\V1\Tools;

use App\Http\Controllers\Controller;
use App\Models\MicroTool;
use App\Services\Tools\ToolRunService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Throwable;

class ToolRunController extends Controller
{
    public function __construct(private ToolRunService $toolRuns) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $tool = MicroTool::query()->active()->where('slug', $slug)->firstOrFail();

        $input = $request->all();
        if (! is_array($input)) {
            $input = [];
        }

        $user = $request->user('sanctum');

        try {
            $result = $this->toolRuns->run($tool, $input, $request, $user);
        } catch (InvalidArgumentException $e) {
            $message = $e->getMessage();
            $status = str_contains(strtolower($message), 'rate limit') ? 429 : 422;

            return response()->json([
                'message' => $message,
            ], $status);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'The tool could not complete this request.',
            ], 500);
        }

        return response()->json([
            'data' => $result['data'],
            'meta' => [
                'run_id' => $result['run_id'],
                'ad_free' => $result['ad_free'],
                'limits_remaining' => $result['limits_remaining'],
            ],
        ]);
    }
}
