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
use App\Services\Content\RelatedContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatedContentController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:article,research_paper,case_study,product',
            'slug' => 'required|string|max:255',
        ]);

        return match ($data['type']) {
            'article' => $this->articlePayload($data['slug']),
            'research_paper' => $this->researchPayload($data['slug']),
            'case_study' => $this->caseStudyPayload($data['slug']),
            'product' => $this->productPayload($data['slug']),
        };
    }

    private function articlePayload(string $slug): JsonResponse
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('view', $article);
        $related = $this->relatedContent->relatedArticles($article);

        return response()->json([
            'type' => 'article',
            'slug' => $article->slug,
            'related' => ArticleResource::collection($related),
        ]);
    }

    private function researchPayload(string $slug): JsonResponse
    {
        $paper = ResearchPaper::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('view', $paper);
        $related = $this->relatedContent->relatedResearchPapers($paper);

        return response()->json([
            'type' => 'research_paper',
            'slug' => $paper->slug,
            'related' => ResearchPaperResource::collection($related),
        ]);
    }

    private function caseStudyPayload(string $slug): JsonResponse
    {
        $study = CaseStudy::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('view', $study);
        $related = $this->relatedContent->relatedCaseStudies($study);

        return response()->json([
            'type' => 'case_study',
            'slug' => $study->slug,
            'related' => CaseStudyResource::collection($related),
        ]);
    }

    private function productPayload(string $slug): JsonResponse
    {
        $product = Product::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('view', $product);
        $related = $this->relatedContent->relatedProducts($product);

        return response()->json([
            'type' => 'product',
            'slug' => $product->slug,
            'related' => ProductResource::collection($related),
        ]);
    }
}
