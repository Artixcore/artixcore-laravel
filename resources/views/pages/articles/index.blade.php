@extends('layouts.app')

@section('meta_title', config('marketing.blog.meta_title'))
@section('meta_description', config('marketing.blog.meta_description'))
@section('og_title', config('marketing.blog.meta_title'))
@section('og_description', config('marketing.blog.meta_description'))

@section('content')
@php
	$heroTitle = 'Artixcore Articles';
	$heroSub = 'Practical articles on AI, SaaS, engineering, and digital strategy.';
@endphp
<section class="position-relative overflow-hidden pt-8 pb-6 border-bottom">
	<div class="container position-relative">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<p class="text-primary fw-semibold mb-2 small text-uppercase letter-spacing-1">Articles</p>
				<h1 class="display-6 fw-bold mb-3">{{ $heroTitle }}</h1>
				<p class="lead text-muted mb-0">{{ $heroSub }}</p>
			</div>
			<div class="col-lg-4 mt-4 mt-lg-0">
				<form action="@if(request()->routeIs('articles.category')){{ route('articles.category', request()->route('categorySlug')) }}@elseif(request()->routeIs('articles.tag')){{ route('articles.tag', request()->route('tagSlug')) }}@else{{ route('articles.index') }}@endif" method="get" class="d-flex gap-2 flex-wrap justify-content-lg-end">
					<input type="search" name="q" value="{{ request('q') }}" placeholder="Search articles…" class="form-control form-control-sm" style="min-width: 200px;">
					<button type="submit" class="btn btn-primary btn-sm px-4">Search</button>
				</form>
			</div>
		</div>
	</div>
</section>

@if($featuredArticle)
<section class="py-5 bg-light bg-opacity-50">
	<div class="container">
		<p class="small text-uppercase text-muted fw-semibold mb-3">Featured</p>
		<div class="card border-0 shadow-sm overflow-hidden">
			<div class="row g-0">
				<div class="col-md-5">
					<a href="{{ route('articles.show', $featuredArticle->slug) }}" class="d-block h-100">
						<img src="{{ $featuredArticle->main_image_url }}" alt="" width="800" height="450" class="w-100 h-100 object-fit-cover" style="min-height: 220px; object-fit: cover;" loading="lazy">
					</a>
				</div>
				<div class="col-md-7">
					<div class="card-body p-4 p-lg-5 d-flex flex-column h-100">
						<div class="small text-muted mb-2">
							@if($featuredArticle->primaryCategoryTerm())
								<a href="{{ route('articles.category', $featuredArticle->primaryCategoryTerm()->slug) }}" class="text-decoration-none">{{ $featuredArticle->primaryCategoryTerm()->name }}</a>
							@endif
							@if($featuredArticle->published_at)
								<span class="mx-2">·</span>{{ $featuredArticle->published_at->format('M j, Y') }}
							@endif
							<span class="mx-2">·</span>{{ $featuredArticle->reading_time_minutes ?? 1 }} min read
						</div>
						<h2 class="h3 fw-bold mb-3"><a href="{{ route('articles.show', $featuredArticle->slug) }}" class="text-reset text-decoration-none stretched-link">{{ $featuredArticle->title }}</a></h2>
						<p class="text-muted mb-4">{{ \Illuminate\Support\Str::limit($featuredArticle->summary, 220) }}</p>
						<div class="mt-auto small text-muted">{{ $featuredArticle->author_name }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endif

<section class="py-5">
	<div class="container">
		@if($category || $tag)
			<p class="text-muted mb-4">
				@if($category)
					Category: <strong>{{ $category->name }}</strong>
				@endif
				@if($tag)
					Tag: <strong>{{ $tag->name }}</strong>
				@endif
				— <a href="{{ route('articles.index') }}">View all</a>
			</p>
		@endif

		@if($categoriesNav->isNotEmpty())
			<div class="mb-4 d-flex flex-wrap gap-2 align-items-center">
				<span class="small text-muted me-1">Topics:</span>
				<a href="{{ route('articles.index') }}" class="badge rounded-pill {{ !request('category') && !request()->routeIs('articles.category') ? 'bg-dark' : 'bg-light text-dark border' }} text-decoration-none">All</a>
				@foreach($categoriesNav as $cat)
					<a href="{{ route('articles.category', $cat->slug) }}" class="badge rounded-pill {{ ($category && $category->id === $cat->id) ? 'bg-dark' : 'bg-light text-dark border' }} text-decoration-none">{{ $cat->name }}</a>
				@endforeach
			</div>
		@endif

		@if($popularTags->isNotEmpty())
			<div class="mb-5 d-flex flex-wrap gap-2 align-items-center">
				<span class="small text-muted me-1">Tags:</span>
				@foreach($popularTags as $t)
					<a href="{{ route('articles.tag', $t->slug) }}" class="small text-decoration-none">{{ $t->name }}</a>@if(!$loop->last)<span class="text-muted">·</span>@endif
				@endforeach
			</div>
		@endif

		@if($articles->isEmpty())
			<div class="text-center py-5">
				<h2 class="h5 mb-2">No articles have been published yet. Please check back soon.</h2>
				<p class="text-muted mb-0">Try clearing filters or searching with different keywords.</p>
			</div>
		@else
			<div class="row g-4">
				@foreach($articles as $article)
					<div class="col-md-6 col-lg-4">
						<article class="card h-100 border-0 shadow-sm overflow-hidden">
							<a href="{{ route('articles.show', $article->slug) }}" class="ratio ratio-16x9 bg-light">
								<img src="{{ $article->main_image_url }}" alt="" width="640" height="360" class="object-fit-cover" loading="lazy">
							</a>
							<div class="card-body d-flex flex-column">
								<div class="small text-muted mb-2">
									@if($article->primaryCategoryTerm())
										<a href="{{ route('articles.category', $article->primaryCategoryTerm()->slug) }}" class="text-decoration-none">{{ $article->primaryCategoryTerm()->name }}</a>
									@else
										<span>Article</span>
									@endif
									@if($article->published_at)
										<span class="mx-1">·</span>{{ $article->published_at->format('M j, Y') }}
									@endif
								</div>
								<h3 class="h5 card-title flex-grow-1">
									<a href="{{ route('articles.show', $article->slug) }}" class="text-reset text-decoration-none stretched-link">{{ $article->title }}</a>
								</h3>
								<p class="small text-muted mb-3">{{ \Illuminate\Support\Str::limit($article->summary, 110) }}</p>
								<div class="small text-muted mt-auto d-flex justify-content-between align-items-center">
									<span>{{ $article->author_name }}</span>
									<span>{{ $article->reading_time_minutes ?? 1 }} min</span>
								</div>
								@if($article->tagTerms()->isNotEmpty())
									<div class="mt-2 pt-2 border-top small">
										@foreach($article->tagTerms()->take(4) as $tg)
											<a href="{{ route('articles.tag', $tg->slug) }}" class="text-decoration-none me-2">{{ $tg->name }}</a>
										@endforeach
									</div>
								@endif
							</div>
						</article>
					</div>
				@endforeach
			</div>
			<div class="mt-5 d-flex justify-content-center">{{ $articles->links() }}</div>
		@endif
	</div>
</section>
@endsection
