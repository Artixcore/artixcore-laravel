<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Article::class);

        return view('admin.articles.index', [
            'articles' => Article::query()->orderByDesc('updated_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('admin.articles.form', [
            'article' => new Article,
            'mode' => 'create',
            'categoryTerms' => $this->categoryTerms(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Article::class);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $termIds = $data['term_ids'] ?? [];
        unset($data['term_ids']);
        $article = Article::query()->create($data);
        $article->terms()->sync($termIds);

        return $this->respond($request, 'Article created.', route('admin.articles.index'));
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);
        $article->load('terms');

        return view('admin.articles.form', [
            'article' => $article,
            'mode' => 'edit',
            'categoryTerms' => $this->categoryTerms(),
        ]);
    }

    public function update(Request $request, Article $article): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $article);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $termIds = $data['term_ids'] ?? [];
        unset($data['term_ids']);
        $article->update($data);
        $article->terms()->sync($termIds);

        return $this->respond($request, 'Article updated.', route('admin.articles.index'));
    }

    public function destroy(Request $request, Article $article): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $article);
        $article->terms()->detach();
        $article->delete();

        return $this->respond($request, 'Article deleted.', route('admin.articles.index'));
    }

    /**
     * @return Collection<int, Term>
     */
    private function categoryTerms(): Collection
    {
        $tax = Taxonomy::query()->where('slug', 'categories')->first();
        if (! $tax) {
            return collect();
        }

        return Term::query()->where('taxonomy_id', $tax->id)->orderBy('sort_order')->orderBy('name')->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($request->route('article')),
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:draft,published'],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'term_ids' => ['nullable', 'array'],
            'term_ids.*' => ['integer', 'exists:terms,id'],
        ];

        $data = $request->validate($rules) + ['featured' => $request->boolean('featured')];

        if (isset($data['body']) && is_string($data['body'])) {
            $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body']);
        }

        return $data;
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
