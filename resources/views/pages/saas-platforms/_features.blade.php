@php
	$p = $saasPage;
	$items = $p['features'] ?? [];
	$items = is_array($items) ? $items : [];
@endphp
@if(!empty($p['features_title']) && count($items) > 0)
<section class="py-5 bg-dark position-relative overflow-hidden" data-bs-theme="dark">
	<div class="container position-relative">
		<div class="inner-container-small text-center mb-4 mb-sm-6" data-aos="fade-up">
			<h2 class="text-white mb-0">{{ $p['features_title'] }}</h2>
			@if(!empty($p['features_subtitle']))
				<p class="mb-0 mt-3 text-white text-opacity-75">{{ $p['features_subtitle'] }}</p>
			@endif
		</div>
		<div class="row g-4 g-lg-5">
			@foreach($items as $i => $item)
				<div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ min($i * 40, 320) }}">
					<div class="icon-lg rounded-circle bg-white bg-opacity-10 text-primary mb-3">
						<i class="{{ !empty($item['icon']) ? $item['icon'] : 'bi bi-grid' }} fa-lg"></i>
					</div>
					<h6 class="text-white mb-2">{{ $item['title'] ?? '' }}</h6>
					<p class="mb-0 small text-white text-opacity-75">{{ $item['body'] ?? '' }}</p>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
