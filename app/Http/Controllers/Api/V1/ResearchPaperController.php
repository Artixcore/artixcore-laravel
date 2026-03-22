<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ResearchPaperResource;
use App\Models\ResearchPaper;
use App\Services\Content\RelatedContentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResearchPaperController extends Controller
{
    public function __construct(private RelatedContentService $relatedContent) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'topic' => 'sometimes|string',
            'q' => 'sometimes|string|max:200',
        ]);

        $query = ResearchPaper::query()->published()->with('terms');

        if (! empty($filters['topic'])) {
            $query->whereHas('terms', function ($q) use ($filters): void {
                $q->where('slug', $filters['topic'])
                    ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'topics'));
            });
        }

        if (! empty($filters['q'])) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)
                    ->orWhere('summary', 'like', $needle);
            });
        }

        $query->orderByDesc('featured')->orderByDesc('published_at');

        return ResearchPaperResource::collection($query->paginate(12));
    }

    public function show(string $slug): ResearchPaperResource
    {
        $paper = ResearchPaper::query()->where('slug', $slug)->with('terms')->firstOrFail();
        $this->authorize('view', $paper);
        $paper->increment('view_count');

        $related = $this->relatedContent->relatedResearchPapers($paper);
        $paper->setRelation('relatedPapers', $related);

        return new ResearchPaperResource($paper);
    }
}
