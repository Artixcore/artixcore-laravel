<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Http\Resources\Api\V1\CaseStudyResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Http\Resources\Api\V1\ResearchPaperResource;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Product;
use App\Models\ResearchPaper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrendingController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $type = $request->validate([
            'type' => 'required|string|in:articles,research_papers,case_studies,products',
        ])['type'];

        return match ($type) {
            'articles' => response()->json([
                'data' => ArticleResource::collection(
                    Article::query()
                        ->published()
                        ->orderByDesc('trending_score')
                        ->orderByDesc('view_count')
                        ->orderByDesc('published_at')
                        ->limit(10)
                        ->get()
                ),
            ]),
            'research_papers' => response()->json([
                'data' => ResearchPaperResource::collection(
                    ResearchPaper::query()
                        ->published()
                        ->orderByDesc('trending_score')
                        ->orderByDesc('view_count')
                        ->orderByDesc('published_at')
                        ->limit(10)
                        ->get()
                ),
            ]),
            'case_studies' => response()->json([
                'data' => CaseStudyResource::collection(
                    CaseStudy::query()
                        ->published()
                        ->orderByDesc('trending_score')
                        ->orderByDesc('view_count')
                        ->orderByDesc('published_at')
                        ->limit(10)
                        ->get()
                ),
            ]),
            'products' => response()->json([
                'data' => ProductResource::collection(
                    Product::query()
                        ->published()
                        ->orderByDesc('trending_score')
                        ->orderByDesc('view_count')
                        ->orderByDesc('published_at')
                        ->limit(10)
                        ->get()
                ),
            ]),
        };
    }
}
