<section class="bg-dark position-relative overflow-hidden" data-bs-theme="dark">
	<div class="container position-relative py-5">
		<div class="inner-container-small text-center mb-5">
			@if(!empty($home['services_badge']))
				<span class="bg-light heading-color small rounded-3 px-3 py-2">{{ $home['services_badge'] }}</span>
			@endif
			<h2 class="mb-0 mt-4 text-white">{{ $home['services_title'] ?? 'Services' }}</h2>
		</div>
		<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 gy-5">
			@forelse($services->take(6) as $service)
				<div class="col" data-aos="fade-up">
					<div class="card bg-light h-100">
						<div class="card-body pb-0">
							<div class="icon-lg bg-white text-primary rounded-circle mb-4 mt-n5">
								<i class="{{ $service->icon ?: 'bi bi-stack' }} fa-fw fs-5"></i>
							</div>
							<h5 class="mb-3"><a href="{{ route('services.show', $service->slug) }}" class="text-decoration-none stretched-link">{{ $service->title }}</a></h5>
							<p>{{ $service->summary }}</p>
						</div>
						<div class="card-footer bg-light mt-auto pt-2">
							<span class="icon-link icon-link-hover">Details<i class="bi bi-arrow-right"></i></span>
						</div>
					</div>
				</div>
			@empty
				<p class="text-white text-center">Services will appear here once published in admin.</p>
			@endforelse
		</div>
		<div class="text-center mt-5">
			<a href="{{ route('services.index') }}" class="btn btn-light mb-0">View all services</a>
		</div>
	</div>
</section>
