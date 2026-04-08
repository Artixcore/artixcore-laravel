<section>
	<div class="container py-5">
		<div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
			<div>
				<h2 class="mb-1">{{ $home['articles_title'] ?? 'Latest articles' }}</h2>
				<p class="mb-0 text-muted">{{ $home['articles_subtitle'] ?? '' }}</p>
			</div>
			<a href="{{ route('blog.index') }}" class="btn btn-outline-primary btn-sm mb-0">All articles</a>
		</div>
		<div class="row g-4">
			@forelse($articles as $article)
				<div class="col-md-4">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<h5 class="card-title"><a href="{{ route('blog.show', $article->slug) }}" class="text-decoration-none">{{ $article->title }}</a></h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($article->summary, 110) }}</p>
						</div>
						<div class="card-footer bg-transparent border-0 small text-muted">
							{{ optional($article->published_at)->format('M j, Y') }}
						</div>
					</div>
				</div>
			@empty
				<p class="text-muted">Publish articles in the admin to show them here.</p>
			@endforelse
		</div>
	</div>
</section>
