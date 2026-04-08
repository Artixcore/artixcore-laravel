@extends('layouts.app')

@section('meta_title', 'Blog — '.($site->site_name ?? 'Artixcore'))

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-2">Articles</h1>
		@if($category)
			<p class="text-muted">Category: <strong>{{ $category->name }}</strong> — <a href="{{ route('blog.index') }}">Clear</a></p>
		@endif
		<div class="row g-4 mt-2">
			@foreach($articles as $article)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<h5><a href="{{ route('blog.show', $article->slug) }}" class="stretched-link text-decoration-none">{{ $article->title }}</a></h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($article->summary, 120) }}</p>
						</div>
						<div class="card-footer bg-transparent border-0 small">{{ optional($article->published_at)->format('M j, Y') }}</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="mt-4">{{ $articles->links() }}</div>
	</div>
</section>
@endsection
