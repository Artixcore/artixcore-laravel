@php $steps = $home['process_steps'] ?? []; @endphp
<section>
	<div class="container py-5">
		<h2 class="text-center mb-5" data-aos="fade-up">{{ $home['process_title'] ?? 'How we work' }}</h2>
		<div class="row g-4 justify-content-center">
			@foreach($steps as $i => $step)
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
					<div class="text-center px-lg-3">
						<div class="icon-lg bg-primary text-white rounded-circle mx-auto mb-3">{{ $i + 1 }}</div>
						<h5>{{ $step['title'] ?? '' }}</h5>
						<p class="mb-0 small">{{ $step['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
