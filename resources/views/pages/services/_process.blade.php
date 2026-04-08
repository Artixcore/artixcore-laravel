@php
	$sp = $servicesPage;
	$steps = $sp['process_steps'] ?? [];
	$steps = is_array($steps) ? $steps : [];
@endphp
@if(!empty($sp['process_title']) && count($steps) > 0)
<section class="py-5">
	<div class="container">
		<h2 class="text-center mb-5" data-aos="fade-up">{{ $sp['process_title'] }}</h2>
		<div class="row g-4 justify-content-center">
			@foreach($steps as $i => $step)
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
					<div class="text-center px-lg-3">
						<div class="icon-lg bg-primary text-white rounded-circle mx-auto mb-3">{{ $i + 1 }}</div>
						<h5>{{ $step['title'] ?? '' }}</h5>
						<p class="mb-0 small text-muted">{{ $step['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
