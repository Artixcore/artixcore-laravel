@php
	$items = $home['why_items'] ?? [];
@endphp
<section class="pt-5">
	<div class="container">
		<div class="text-center mb-5" data-aos="fade-up">
			<h2 class="mb-0">{{ $home['why_title'] ?? 'Why Artixcore' }}</h2>
		</div>
		<div class="row g-4">
			@foreach($items as $i => $item)
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
					<div class="card border-0 bg-light h-100 p-4">
						<h5 class="card-title">{{ $item['title'] ?? '' }}</h5>
						<p class="mb-0">{{ $item['body'] ?? '' }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
