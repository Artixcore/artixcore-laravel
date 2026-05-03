@php
	$platforms = $platforms ?? collect();
@endphp
@if($platforms->isNotEmpty())
<section class="py-6 bg-light bg-opacity-50 border-bottom">
	<div class="container">
		<h2 class="h4 fw-bold mb-4">Platforms</h2>
		<p class="text-muted mb-4">Explore each SaaS product page for features, use cases, related articles, and FAQs.</p>
		<div class="row g-4">
			@foreach($platforms as $platform)
				<div class="col-md-6 col-lg-4">
					<a href="{{ route('saas-platforms.show', $platform->slug) }}" class="text-reset text-decoration-none card border-0 shadow-sm h-100 overflow-hidden">
						<div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
							<img src="{{ $platform->main_image_url }}" alt="" class="object-fit-cover" loading="lazy" width="640" height="360">
						</div>
						<div class="card-body">
							<h3 class="h6 mb-1">{{ $platform->title }}</h3>
							@if($platform->tagline)
								<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($platform->tagline, 120) }}</p>
							@elseif($platform->summary)
								<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($platform->summary, 120) }}</p>
							@endif
						</div>
					</a>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
