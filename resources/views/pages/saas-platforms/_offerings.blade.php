@php
	$p = $saasPage;
	$items = $p['offerings'] ?? [];
	$items = is_array($items) ? $items : [];
@endphp
@if(!empty($p['offerings_title']) && count($items) > 0)
<section class="py-5 bg-light">
	<div class="container">
		<div class="inner-container text-center mb-4 mb-sm-6" data-aos="fade-up">
			<h2 class="mb-0">{{ $p['offerings_title'] }}</h2>
			@if(!empty($p['offerings_subtitle']))
				<p class="mb-0 mt-3 text-muted">{{ $p['offerings_subtitle'] }}</p>
			@endif
		</div>
		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
			@foreach($items as $i => $item)
				<div class="col" data-aos="fade-up" data-aos-delay="{{ min($i * 50, 400) }}">
					<div class="card border-0 shadow-sm h-100 p-4">
						<div class="icon-lg rounded-circle heading-color bg-white border mb-3">
							<i class="{{ !empty($item['icon']) ? $item['icon'] : 'bi bi-box-seam' }} fa-lg"></i>
						</div>
						<h5 class="mb-3">{{ $item['title'] ?? '' }}</h5>
						<p class="mb-0 small text-muted">{{ $item['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
