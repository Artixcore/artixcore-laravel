@php
	$s = $section ?? [];
	$items = $s['items'] ?? [];
@endphp
@if(count($items) > 0)
<section class="bg-dark position-relative overflow-hidden" data-bs-theme="dark">
	<div class="container position-relative py-5">
		<div class="inner-container-small text-center mb-5">
			@if(!empty($s['badge_text']))
				<span class="bg-light heading-color small rounded-3 px-3 py-2">{{ $s['badge_text'] }}</span>
			@endif
			<h2 class="mb-0 mt-4 text-white">{{ $s['title'] ?? 'Services' }}</h2>
			@if(!empty($s['subtitle']))
				<p class="text-white-50 mb-0 mt-2">{{ $s['subtitle'] }}</p>
			@endif
		</div>
		<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 gy-5">
			@foreach($items as $it)
				<div class="col" data-aos="fade-up">
					<div class="card bg-light h-100">
						<div class="card-body pb-0">
							<div class="icon-lg bg-white text-primary rounded-circle mb-4 mt-n5">
								<i class="{{ $it['icon'] ?? 'bi bi-stack' }} fa-fw fs-5"></i>
							</div>
							<h5 class="mb-3">
								<a href="{{ url($it['url'] ?? '#') }}" class="text-decoration-none stretched-link text-dark">{{ $it['title'] ?? '' }}</a>
							</h5>
							<p>{{ $it['summary'] ?? '' }}</p>
						</div>
						<div class="card-footer bg-light mt-auto pt-2">
							<span class="icon-link icon-link-hover">Details<i class="bi bi-arrow-right"></i></span>
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="text-center mt-5">
			<a href="{{ route('services.index') }}" class="btn btn-light mb-0">View all services</a>
		</div>
	</div>
</section>
@endif
