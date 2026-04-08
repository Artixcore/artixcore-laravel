@php
	$p = $saasPage;
	$items = $p['use_cases'] ?? [];
	$items = is_array($items) ? $items : [];
@endphp
@if(!empty($p['use_cases_title']) && count($items) > 0)
<section class="py-5 bg-light">
	<div class="container">
		<h2 class="text-center mb-4 mb-sm-5" data-aos="fade-up">{{ $p['use_cases_title'] }}</h2>
		<div class="row g-3 g-md-4">
			@foreach($items as $i => $item)
				<div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ min($i * 40, 280) }}">
					<div class="card border h-100 p-4">
						<h6 class="mb-2">{{ $item['title'] ?? '' }}</h6>
						<p class="mb-0 small text-muted">{{ $item['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
