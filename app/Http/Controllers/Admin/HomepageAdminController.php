<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderHomepageSectionsRequest;
use App\Http\Requests\Admin\StoreHomepageSectionItemRequest;
use App\Http\Requests\Admin\UpdateHomepageSectionItemRequest;
use App\Http\Requests\Admin\UpdateHomepageSectionRequest;
use App\Http\Requests\Admin\UpdateHomepageSeoRequest;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\HomepageContentResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class HomepageAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function index(): View
    {
        $this->authorize('update', SiteSetting::instance());

        $sections = HomepageSection::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['items' => function ($q): void {
                $q->orderBy('sort_order')->orderBy('id');
            }])
            ->get();

        $site = SiteSetting::instance();
        $mergedSeo = app(HomepageContentResolver::class)->mergeHomepageSeo($site);

        return view('admin.homepage.index', [
            'sections' => $sections,
            'mergedSeo' => $mergedSeo,
            'picklists' => $this->buildPicklists(),
            'itemTypeLabels' => $this->itemTypeLabels(),
        ]);
    }

    public function updateSection(UpdateHomepageSectionRequest $request, HomepageSection $homepage_section): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $validated = $request->validated();
        if ($request->filled('settings_json')) {
            $decoded = json_decode((string) $request->input('settings_json'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['settings'] = $decoded;
            }
        }
        unset($validated['settings_json']);

        $fill = Arr::only($validated, [
            'key', 'title', 'subtitle', 'description', 'badge_text',
            'button_text', 'button_url', 'secondary_button_text', 'secondary_button_url',
            'image_path', 'is_enabled', 'sort_order', 'settings',
        ]);
        $homepage_section->fill($fill);
        $homepage_section->save();

        return $this->adminRespond($request, 'Section saved.');
    }

    public function uploadSectionImage(Request $request, HomepageSection $homepage_section): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());
        $request->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $path = $request->file('image')->store('homepage', 'public');
        $homepage_section->image_path = 'storage/'.$path;
        $homepage_section->save();

        return $this->adminRespond($request, 'Image updated.', null, ['image_path' => $homepage_section->image_path]);
    }

    public function reorder(ReorderHomepageSectionsRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $order = $request->validated('order');
        foreach ($order as $i => $id) {
            HomepageSection::query()->whereKey($id)->update(['sort_order' => $i]);
        }

        return $this->adminRespond($request, 'Order saved.');
    }

    public function storeItem(StoreHomepageSectionItemRequest $request, HomepageSection $homepage_section): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $data = $request->validated();
        $max = (int) HomepageSectionItem::query()->where('homepage_section_id', $homepage_section->id)->max('sort_order');
        HomepageSectionItem::query()->create([
            'homepage_section_id' => $homepage_section->id,
            'item_type' => $data['item_type'],
            'item_id' => $data['item_id'],
            'title_override' => $data['title_override'] ?? null,
            'description_override' => $data['description_override'] ?? null,
            'image_override' => $data['image_override'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'button_url' => $data['button_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? ($max + 1),
            'is_enabled' => true,
        ]);

        return $this->adminRespond($request, 'Item attached.');
    }

    public function updateItem(UpdateHomepageSectionItemRequest $request, HomepageSectionItem $homepage_section_item): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $homepage_section_item->fill($request->validated());
        $homepage_section_item->save();

        return $this->adminRespond($request, 'Item updated.');
    }

    public function destroyItem(Request $request, HomepageSectionItem $homepage_section_item): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $homepage_section_item->delete();

        return $this->adminRespond($request, 'Item removed.');
    }

    public function updateSeo(UpdateHomepageSeoRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('update', SiteSetting::instance());

        $settings = SiteSetting::instance();
        $current = is_array($settings->homepage_seo) ? $settings->homepage_seo : [];
        $settings->homepage_seo = array_merge($current, $request->validated());
        $settings->save();

        return $this->adminRespond($request, 'Homepage SEO saved.');
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function buildPicklists(): array
    {
        $out = [
            'service' => [],
            'product' => [],
            'portfolio_item' => [],
            'case_study' => [],
            'article' => [],
            'testimonial' => [],
            'faq' => [],
        ];

        try {
            $out['service'] = Service::query()->orderBy('title')->limit(300)->get(['id', 'title', 'status'])->map(fn (Service $s) => [
                'id' => $s->id,
                'label' => $s->title.' ('.$s->status.')',
            ])->all();
        } catch (\Throwable) {
        }

        try {
            $out['product'] = Product::query()->orderBy('title')->limit(300)->get(['id', 'title', 'status'])->map(fn (Product $p) => [
                'id' => $p->id,
                'label' => $p->title.' ('.$p->status.')',
            ])->all();
        } catch (\Throwable) {
        }

        try {
            $out['portfolio_item'] = PortfolioItem::query()->orderBy('title')->limit(300)->get(['id', 'title', 'status'])->map(fn (PortfolioItem $p) => [
                'id' => $p->id,
                'label' => $p->title.' ('.$p->status.')',
            ])->all();
        } catch (\Throwable) {
        }

        try {
            $out['case_study'] = CaseStudy::query()->orderBy('title')->limit(300)->get(['id', 'title', 'status'])->map(fn (CaseStudy $c) => [
                'id' => $c->id,
                'label' => $c->title.' ('.$c->status.')',
            ])->all();
        } catch (\Throwable) {
        }

        try {
            $out['article'] = Article::query()->orderByDesc('id')->limit(300)->get(['id', 'title', 'status'])->map(fn (Article $a) => [
                'id' => $a->id,
                'label' => $a->title.' ('.$a->status.')',
            ])->all();
        } catch (\Throwable) {
        }

        try {
            $out['testimonial'] = Testimonial::query()->orderByDesc('id')->limit(300)->get(['id', 'author_name', 'moderation_status'])->map(fn (Testimonial $t) => [
                'id' => $t->id,
                'label' => $t->author_name.' ('.$t->moderation_status.')',
            ])->all();
        } catch (\Throwable) {
        }

        try {
            $out['faq'] = Faq::query()->orderBy('question')->limit(300)->get(['id', 'question', 'status', 'is_published'])->map(fn (Faq $f) => [
                'id' => $f->id,
                'label' => \Illuminate\Support\Str::limit($f->question, 80).' ('.($f->status ?? '—').')',
            ])->all();
        } catch (\Throwable) {
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    private function itemTypeLabels(): array
    {
        return [
            'service' => 'Service',
            'product' => 'SaaS platform (Product)',
            'portfolio_item' => 'Portfolio item',
            'case_study' => 'Case study',
            'article' => 'Article',
            'testimonial' => 'Testimonial',
            'faq' => 'FAQ',
        ];
    }
}
