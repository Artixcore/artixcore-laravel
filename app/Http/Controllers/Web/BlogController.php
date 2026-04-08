<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::query()->published()->orderByDesc('published_at');
        $category = null;

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $taxonomy = Taxonomy::query()->where('slug', 'categories')->first();
            if ($taxonomy) {
                $term = Term::query()->where('taxonomy_id', $taxonomy->id)->where('slug', $slug)->first();
                if ($term) {
                    $category = $term;
                    $query->whereHas('terms', fn ($q) => $q->where('terms.id', $term->id));
                }
            }
        }

        return view('pages.blog.index', [
            'articles' => $query->paginate(9)->withQueryString(),
            'category' => $category,
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::query()->published()->where('slug', $slug)->firstOrFail();

        return view('pages.blog.show', [
            'article' => $article,
        ]);
    }
}
