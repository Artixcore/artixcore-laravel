@php $p = $saasPage; @endphp
@if($highlightedServices->isNotEmpty())
<section class="py-5">
	<div class="container">
		<div class="inner-container text-center mb-4 mb-sm-6" data-aos="fade-up">
			<h2 class="mb-0">{{ $p['highlighted_services_title'] ?? 'Related services' }}</h2>
			@if(!empty($p['highlighted_services_subtitle']))
				<p class="mb-0 mt-3 text-muted">{{ $p['highlighted_services_subtitle'] }}</p>
			@endif
		</div>
		<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
			@foreach($highlightedServices as $i => $service)
				<div class="col" data-aos="fade-up" data-aos-delay="{{ min($i * 50, 400) }}">
					<div class="card card-hover-shadow border h-100 p-4 position-relative">
						<div class="card-body p-0 d-flex flex-column">
							@php $saasImgFallback = asset('theme/images/services/4by3/01.jpg'); @endphp
							@if($service->featuredImageMedia)
								<div class="mb-4 rounded overflow-hidden ratio ratio-16x9">
									<img
										src="{{ $service->featuredImageMedia->absoluteUrl() }}"
										class="w-100 h-100 object-fit-cover"
										alt="{{ $service->title }}"
										loading="lazy"
										width="1200"
										height="675"
										onerror="this.onerror=null;this.src='{{ $saasImgFallback }}'"
									>
								</div>
							@else
								<figure class="text-primary mb-4">
									<span class="icon-lg"><i class="{{ $service->icon ?: 'bi bi-grid' }} fs-3"></i></span>
								</figure>
							@endif
							<h5 class="mb-3">
								<a href="{{ route('services.show', $service->slug) }}" class="text-decoration-none stretched-link">{{ $service->title }}</a>
							</h5>
							@if($service->summary)
								<p class="mb-0 small">{{ $service->summary }}</p>
							@endif
						</div>
						<div class="card-footer mt-auto p-0 pt-3 bg-transparent border-0">
							<a class="icon-link icon-link-hover z-index-2" href="{{ route('services.show', $service->slug) }}">View detail<i class="bi bi-arrow-right"></i></a>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
