@php
	$s = $section ?? [];
	$st = $s['settings'] ?? [];
	$whyItems = $st['why_items'] ?? [];
	$stat1v = $st['stat_1_value'] ?? '10+';
	$stat1l = $st['stat_1_label'] ?? 'Years shipping production systems';
	$stat2v = $st['stat_2_value'] ?? '120+';
	$stat2l = $st['stat_2_label'] ?? 'Launches & major releases';
	$imgA = $st['image_left'] ?? null;
	$imgB = $st['image_right'] ?? null;
	$u = function (?string $p, string $fb): string {
		if ($p === null || $p === '') {
			return $fb;
		}
		if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://')) {
			return $p;
		}

		return asset(ltrim($p, '/'));
	};
	$leftImg = $u(is_string($imgA) ? $imgA : null, asset('theme/images/about/02.jpg'));
	$rightImg = $u(is_string($imgB) ? $imgB : null, asset('theme/images/about/01.jpg'));
@endphp
<section>
	<div class="container">
		<div class="row g-4 align-items-xl-center">
			<div class="col-lg-7" data-aos="fade-up">
				<div class="row pe-xl-5">
					<div class="col-sm-6">
						<img src="{{ $leftImg }}" class="rounded w-100" alt="" loading="lazy" width="600" height="400" onerror="this.src='{{ asset('theme/images/about/02.jpg') }}'">
					</div>
					<div class="col-sm-6">
						<div class="row mb-4">
							<div class="col-sm-6 mb-4 mb-sm-0">
								<div class="bg-dark text-white rounded text-center p-3">
									<span class="h2 text-white">{{ $stat1v }}</span>
									<p class="mb-0 small">{{ $stat1l }}</p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="bg-primary rounded text-center p-3">
									<span class="h2 text-white">{{ $stat2v }}</span>
									<p class="mb-0 text-white small">{{ $stat2l }}</p>
								</div>
							</div>
						</div>
						<img src="{{ $rightImg }}" class="rounded w-100" alt="" loading="lazy" width="600" height="400" onerror="this.src='{{ asset('theme/images/about/01.jpg') }}'">
					</div>
				</div>
			</div>
			<div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
				@if(!empty($s['badge_text']))
					<span class="heading-color bg-light small rounded-3 px-3 py-2">{{ $s['badge_text'] }}</span>
				@endif
				<h2 class="my-4">{{ $s['title'] ?? 'Software, strategy, and execution in one team' }}</h2>
				<p class="mb-4">{{ $s['description'] ?? $s['subtitle'] ?? '' }}</p>
				<a href="{{ route('about') }}" class="btn btn-dark mb-0">About Artixcore</a>
			</div>
		</div>
		@if(is_array($whyItems) && count($whyItems) > 0)
			<div class="row g-4 pt-5">
				@foreach($whyItems as $i => $item)
					<div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
						<div class="card border-0 bg-light h-100 p-4">
							<h5 class="card-title">{{ is_array($item) ? ($item['title'] ?? '') : '' }}</h5>
							<p class="mb-0">{{ is_array($item) ? ($item['body'] ?? '') : '' }}</p>
						</div>
					</div>
				@endforeach
			</div>
		@endif
	</div>
</section>
