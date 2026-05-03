<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\Page;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Service;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CrmFaqController extends Controller
{
    use RespondsWithAdminJson;

    /** @var array<string, class-string> */
    private const FAQABLE_TYPES = [
        'service' => Service::class,
        'product' => Product::class,
        'portfolio_item' => PortfolioItem::class,
        'case_study' => CaseStudy::class,
        'article' => Article::class,
        'page' => Page::class,
    ];

    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Faq::class);

        $q = Faq::query()->orderBy('sort_order')->orderBy('question');

        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }
        if ($request->filled('q')) {
            $needle = '%'.$request->string('q')->toString().'%';
            $q->where(function ($w) use ($needle): void {
                $w->where('question', 'like', $needle)
                    ->orWhere('answer', 'like', $needle);
            });
        }

        return view('admin.crm.faqs.index', [
            'faqs' => $q->paginate(30)->withQueryString(),
            'filters' => $request->only(['status', 'q']),
            'faqableTypeLabels' => array_keys(self::FAQABLE_TYPES),
        ]);
    }

    public function attach(Request $request, Faq $faq): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $faq);

        $request->validate([
            'faqable_key' => ['required', 'string', Rule::in(array_keys(self::FAQABLE_TYPES))],
            'faqable_id' => ['required', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $class = self::FAQABLE_TYPES[$request->string('faqable_key')->toString()];
        $entity = $class::query()->findOrFail($request->integer('faqable_id'));
        $order = $request->integer('sort_order', 0);

        if (! $entity->faqs()->where('faqs.id', $faq->id)->exists()) {
            $entity->faqs()->attach($faq->id, ['sort_order' => $order]);
        } else {
            $entity->faqs()->updateExistingPivot($faq->id, ['sort_order' => $order]);
        }

        $this->activityLogger->log('crm.faq.attached', $faq, [
            'faqable_type' => $class,
            'faqable_id' => $entity->getKey(),
        ], $request);

        return $this->adminRespond($request, 'FAQ linked to content.', route('admin.crm.faqs.index'));
    }

    public function detach(Request $request, Faq $faq): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $faq);

        $request->validate([
            'faqable_key' => ['required', 'string', Rule::in(array_keys(self::FAQABLE_TYPES))],
            'faqable_id' => ['required', 'integer', 'min:1'],
        ]);

        $class = self::FAQABLE_TYPES[$request->string('faqable_key')->toString()];
        $entity = $class::query()->findOrFail($request->integer('faqable_id'));
        $entity->faqs()->detach($faq->id);

        $this->activityLogger->log('crm.faq.detached', $faq, [
            'faqable_type' => $class,
            'faqable_id' => $entity->getKey(),
        ], $request);

        return $this->adminRespond($request, 'FAQ unlinked.', route('admin.crm.faqs.index'));
    }
}
