@php
	$sp = $servicesPage;
	$items = $sp['why_items'] ?? [];
	$items = is_array($items) ? $items : [];
@endphp
@if(!empty($sp['why_title']) && count($items) > 0)
<section class="py-5">
	<div class="container">
		<h2 class="text-center mb-4 mb-sm-5" data-aos="fade-up">{{ $sp['why_title'] }}</h2>
		<div class="row g-4">
			@foreach($items as $i => $item)
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
					<div class="card card-body bg-light border-0 h-100 p-4">
						<h5 class="mb-3">{{ $item['title'] ?? '' }}</h5>
						<p class="mb-0 small">{{ $item['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
