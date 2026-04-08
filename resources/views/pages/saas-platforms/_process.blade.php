@php
	$p = $saasPage;
	$steps = $p['process_steps'] ?? [];
	$steps = is_array($steps) ? $steps : [];
@endphp
@if(!empty($p['process_title']) && count($steps) > 0)
<section class="py-5">
	<div class="container">
		<h2 class="text-center mb-5" data-aos="fade-up">{{ $p['process_title'] }}</h2>
		<div class="row g-4 g-lg-5 justify-content-center row-cols-2 row-cols-md-3 row-cols-lg-5">
			@foreach($steps as $i => $step)
				<div class="col" data-aos="fade-up" data-aos-delay="{{ $i * 70 }}">
					<div class="text-center px-lg-2">
						<div class="icon-lg bg-primary text-white rounded-circle mx-auto mb-3">{{ $i + 1 }}</div>
						<h6 class="mb-2">{{ $step['title'] ?? '' }}</h6>
						<p class="mb-0 small text-muted">{{ $step['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
