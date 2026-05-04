@php
	$s = $section ?? [];
	$st = $s['settings'] ?? [];
	$metrics = $st['metrics'] ?? [];
	if (! is_array($metrics) || $metrics === []) {
		$metrics = [
			['value' => '10+', 'label' => 'Years shipping production systems'],
			['value' => '120+', 'label' => 'Launches & major releases'],
		];
	}
@endphp
@if(count($metrics) > 0)
<section class="pt-4 pb-0">
	<div class="container">
		<div class="row g-4 justify-content-center text-center">
			@foreach($metrics as $i => $m)
				@php
					$val = is_array($m) ? ($m['value'] ?? $m['label'] ?? '') : (string) $m;
					$lab = is_array($m) ? ($m['label'] ?? '') : '';
				@endphp
				<div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
					<div class="p-4 rounded-3 border bg-light h-100">
						<div class="h2 mb-1 text-primary">{{ $val }}</div>
						<p class="mb-0 small text-muted">{{ $lab }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
