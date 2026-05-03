@php
	$video = $article->video_embed;
	$relatedArticles = $relatedArticles ?? collect();
@endphp
<section class="pt-8 pb-4 border-bottom bg-light bg-opacity-25">
	<div class="container">
		<nav aria-label="breadcrumb" class="mb-4">
			<ol class="breadcrumb mb-0 small">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
				@if($article->primaryCategoryTerm())
					<li class="breadcrumb-item"><a href="{{ route('articles.category', $article->primaryCategoryTerm()->slug) }}">{{ $article->primaryCategoryTerm()->name }}</a></li>
				@endif
				<li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($article->title, 48) }}</li>
			</ol>
		</nav>
		<div class="row justify-content-center">
			<div class="col-xl-10 col-lg-11">
				<div class="small text-muted mb-3 d-flex flex-wrap gap-3 align-items-center">
					@if($article->primaryCategoryTerm())
						<a href="{{ route('articles.category', $article->primaryCategoryTerm()->slug) }}" class="text-decoration-none fw-semibold">{{ $article->primaryCategoryTerm()->name }}</a>
					@endif
					@if($article->subcategoryTerm())
						<span><a href="{{ route('articles.category', $article->subcategoryTerm()->slug) }}" class="text-decoration-none">{{ $article->subcategoryTerm()->name }}</a></span>
					@endif
					@if($article->published_at)<span>{{ $article->published_at->format('F j, Y') }}</span>@endif
					@if($article->updated_at)<span>Updated {{ $article->updated_at->format('M j, Y') }}</span>@endif
					<span>{{ $article->reading_time_minutes ?? 1 }} min read</span>
					<span class="text-reset">{{ $article->author_name }}</span>
				</div>
				<h1 class="display-6 fw-bold mb-3">{{ $article->title }}</h1>
				@if($article->summary)
					<p class="lead text-muted mb-0">{{ $article->summary }}</p>
				@endif
			</div>
		</div>
	</div>
</section>

<section class="py-4">
	<div class="container">
		<div class="ratio ratio-21x9 rounded overflow-hidden shadow-sm bg-light">
			<img src="{{ $article->main_image_url }}" alt="" width="1200" height="514" class="object-fit-cover w-100 h-100" loading="eager">
		</div>
	</div>
</section>

<section class="pb-8">
	<div class="container">
		<div class="row g-5 justify-content-center">
			@if(count($toc) >= 1)
				<div class="col-lg-3 order-lg-2">
					<div class="sticky-top pt-2" style="top: 6rem;">
						<p class="small fw-semibold text-uppercase text-muted mb-3">On this page</p>
						<nav class="small border-start ps-3">
							@foreach($toc as $item)
								<a href="#{{ $item['id'] }}" class="d-block py-1 text-decoration-none {{ $item['level'] === 3 ? 'ps-3 text-muted' : '' }}">{{ $item['text'] }}</a>
							@endforeach
						</nav>
					</div>
				</div>
			@endif
			<div class="{{ count($toc) >= 1 ? 'col-lg-7 order-lg-1' : 'col-lg-8' }}">
				@if($video)
					<div class="mb-5 ratio ratio-16x9 rounded overflow-hidden shadow-sm bg-dark">
						<iframe src="{{ $video['embed_url'] }}" title="Article video" class="border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
					</div>
				@endif

				@foreach($article->getMedia('article_gallery') as $gm)
					<figure class="mb-4">
						<img src="{{ $gm->getUrl() }}" alt="{{ $gm->name }}" class="img-fluid rounded shadow-sm w-100" loading="lazy" width="{{ $gm->getCustomProperty('width') ?? 1200 }}" height="{{ $gm->getCustomProperty('height') ?? 675 }}">
					</figure>
				@endforeach

				<article class="prose-lg article-body">
					{!! $articleBodyHtml !!}
				</article>

				<div class="mt-5 pt-4 border-top">
					@include('pages.articles.partials.share', ['url' => $shareUrl, 'title' => $article->title])
				</div>

				@if($article->tagTerms()->isNotEmpty())
					<div class="mt-4 d-flex flex-wrap gap-2">
						@foreach($article->tagTerms() as $tg)
							<a href="{{ route('articles.tag', $tg->slug) }}" class="badge bg-light text-dark border text-decoration-none">{{ $tg->name }}</a>
						@endforeach
					</div>
				@endif
			</div>
		</div>
	</div>
</section>

@php
	$linkedServices = $linkedServices ?? collect();
	$linkedPlatforms = $linkedPlatforms ?? collect();
	$relatedCaseStudiesGraph = $relatedCaseStudiesGraph ?? collect();
@endphp
@if($linkedServices->isNotEmpty() || $linkedPlatforms->isNotEmpty())
<section class="py-5 bg-light bg-opacity-50 border-top">
	<div class="container">
		<h2 class="h5 fw-bold mb-3">Related offerings</h2>
		<ul class="list-unstyled d-flex flex-wrap gap-3 mb-0">
			@foreach($linkedServices as $svc)
				<li><a href="{{ route('services.show', $svc->slug) }}" class="badge rounded-pill text-bg-light border text-decoration-none">{{ $svc->title }}</a></li>
			@endforeach
			@foreach($linkedPlatforms as $plat)
				<li><a href="{{ route('saas-platforms.show', $plat->slug) }}" class="badge rounded-pill text-bg-primary-subtle text-primary-emphasis text-decoration-none">{{ $plat->title }}</a></li>
			@endforeach
		</ul>
	</div>
</section>
@endif

@if($relatedCaseStudiesGraph->isNotEmpty())
<section class="py-5 border-top">
	<div class="container">
		<h2 class="h5 fw-bold mb-4">Related case studies</h2>
		<div class="row g-4">
			@foreach($relatedCaseStudiesGraph as $cs)
				<div class="col-md-6 col-lg-4">
					<a href="{{ route('case-studies.show', $cs->slug) }}" class="stretched-link text-decoration-none card border-0 shadow-sm h-100">
						<div class="card-body">
							<h3 class="h6">{{ $cs->title }}</h3>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($cs->outcome_summary ?? '', 120) }}</p>
						</div>
					</a>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@if($relatedArticles->isNotEmpty())
<section class="py-6 bg-light bg-opacity-50 border-top">
	<div class="container">
		<h2 class="h4 fw-bold mb-4">Related articles</h2>
		<div class="row g-4">
			@foreach($relatedArticles as $rel)
				<div class="col-md-6 col-lg-3">
					<a href="{{ route('articles.show', $rel->slug) }}" class="text-reset text-decoration-none d-block card border-0 shadow-sm h-100 overflow-hidden">
						<div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
							<img src="{{ $rel->main_image_url }}" alt="" class="object-fit-cover" loading="lazy" width="400" height="225">
						</div>
						<div class="card-body">
							<h3 class="h6 mb-0">{{ $rel->title }}</h3>
						</div>
					</a>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

<section class="py-6 border-top">
	<div class="container text-center">
		<h2 class="h5 mb-3">Ready to build?</h2>
		<p class="text-muted mb-4">Tell us about your product — we design and ship premium Laravel & React platforms.</p>
		<a href="{{ route('lead.create') }}" class="btn btn-primary btn-lg">Start a project</a>
	</div>
</section>
