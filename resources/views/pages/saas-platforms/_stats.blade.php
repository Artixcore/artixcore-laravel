@php
	$p = $saasPage;
	$stats = $p['stats'] ?? [];
	$stats = is_array($stats) ? $stats : [];
@endphp
@if(!empty($p['show_stats']) && count($stats) > 0)
<section class="pt-5 pb-0">
	<div class="container">
		@if(!empty($p['stats_title']))
			<h2 class="text-center mb-4 mb-sm-5" data-aos="fade-up">{{ $p['stats_title'] }}</h2>
		@endif
		<div class="row g-4 justify-content-center">
			@foreach($stats as $i => $row)
				@if(!empty($row['value']) || !empty($row['label']))
					<div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
						<div class="text-center p-4 rounded-3 bg-light h-100">
							@if(!empty($row['value']))
								<div class="h3 text-primary mb-2">{{ $row['value'] }}</div>
							@endif
							@if(!empty($row['label']))
								<p class="mb-0 small text-muted">{{ $row['label'] }}</p>
							@endif
						</div>
					</div>
				@endif
			@endforeach
		</div>
	</div>
</section>
@endif
