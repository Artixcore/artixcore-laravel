<section class="pt-8 pb-5">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('market-updates.index') }}">Market updates</a></li>
				<li class="breadcrumb-item active">{{ $update->title }}</li>
			</ol>
		</nav>
		@if($update->market_area)<span class="badge bg-info-subtle text-info-emphasis mb-2">{{ $update->market_area }}</span>@endif
		<h1 class="mb-3">{{ $update->title }}</h1>
		@if($update->excerpt)<p class="lead text-muted">{{ $update->excerpt }}</p>@endif
		@if($update->terms->isNotEmpty())
			<div class="mb-4">
				@foreach($update->terms as $term)
					@if(($term->taxonomy?->slug ?? '') === 'categories')
						<a href="{{ route('market-updates.category', $term->slug) }}" class="badge text-bg-light border me-1">{{ $term->name }}</a>
					@elseif(($term->taxonomy?->slug ?? '') === 'tags')
						<a href="{{ route('market-updates.tag', $term->slug) }}" class="badge text-bg-primary-subtle text-primary-emphasis me-1">{{ $term->name }}</a>
					@endif
				@endforeach
			</div>
		@endif
		<img src="{{ $update->main_image_url }}" alt="" class="img-fluid rounded shadow-sm mb-5 w-100 object-fit-cover" style="max-height:380px;" loading="lazy">
	</div>
</section>
<section class="pb-4">
	<div class="container prose-lg">
		@if(($sectionsHtml['trend_summary'] ?? '') !== '')
			<h2 class="h4">Trend summary</h2>
			<div class="mb-5">{!! $sectionsHtml['trend_summary'] !!}</div>
		@endif
		@if(($sectionsHtml['business_impact'] ?? '') !== '')
			<h2 class="h4">Business impact</h2>
			<div class="mb-5">{!! $sectionsHtml['business_impact'] !!}</div>
		@endif
		@if(($sectionsHtml['technology_impact'] ?? '') !== '')
			<h2 class="h4">Technology impact</h2>
			<div class="mb-5">{!! $sectionsHtml['technology_impact'] !!}</div>
		@endif
		@if(($sectionsHtml['opportunities'] ?? '') !== '')
			<h2 class="h4">Opportunities</h2>
			<div class="mb-5">{!! $sectionsHtml['opportunities'] !!}</div>
		@endif
		@if(($sectionsHtml['risks'] ?? '') !== '')
			<h2 class="h4">Risks</h2>
			<div class="mb-5">{!! $sectionsHtml['risks'] !!}</div>
		@endif
		@if(($sectionsHtml['what_next'] ?? '') !== '')
			<h2 class="h4">What businesses should explore next</h2>
			<div class="mb-5">{!! $sectionsHtml['what_next'] !!}</div>
		@endif
		@if(!empty($bodyHtml))
			<h2 class="h4">Full narrative</h2>
			<div class="mb-5">{!! $bodyHtml !!}</div>
		@endif
		@if(is_array($videoEmbed ?? null) && !empty($videoEmbed['embed_url']))
			<div class="ratio ratio-16x9 mb-5">
				<iframe src="{{ $videoEmbed['embed_url'] }}" title="Video" allowfullscreen class="rounded border-0"></iframe>
			</div>
		@endif
	</div>
</section>
@if(isset($relatedMarketUpdates) && $relatedMarketUpdates->isNotEmpty())
<section class="pb-6 bg-light">
	<div class="container">
		<h2 class="h4 mb-4">Related updates</h2>
		<div class="row g-4">
			@foreach($relatedMarketUpdates as $rel)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<h5 class="card-title"><a href="{{ route('market-updates.show', $rel->slug) }}" class="stretched-link text-decoration-none">{{ $rel->title }}</a></h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags((string) $rel->excerpt), 120) }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
<section class="py-6">
	<div class="container text-center">
		<h2 class="h4 mb-3">Discuss what this means for your roadmap</h2>
		<a href="{{ route('lead.create') }}" class="btn btn-primary btn-lg">Talk with our team</a>
	</div>
</section>
