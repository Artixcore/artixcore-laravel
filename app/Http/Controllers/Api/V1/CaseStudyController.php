<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CaseStudyResource;
use App\Models\CaseStudy;
use App\Services\Content\RelatedContentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CaseStudyController extends Controller
{
    public function __construct(private RelatedContentService $relatedContent) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'q' => 'sometimes|string|max:200',
        ]);

        $query = CaseStudy::query()->published()->with('terms');

        if (! empty($filters['q'])) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)
                    ->orWhere('summary', 'like', $needle)
                    ->orWhere('client_name', 'like', $needle);
            });
        }

        $query->orderByDesc('featured')->orderByDesc('published_at');

        return CaseStudyResource::collection($query->paginate(12));
    }

    public function show(string $slug): CaseStudyResource
    {
        $study = CaseStudy::query()->where('slug', $slug)->with('terms')->firstOrFail();
        $this->authorize('view', $study);
        $study->increment('view_count');

        $related = $this->relatedContent->relatedCaseStudies($study);
        $study->setRelation('relatedStudies', $related);

        return new CaseStudyResource($study);
    }
}
