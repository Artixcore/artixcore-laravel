@php
	$s = $section ?? [];
	$st = $s['settings'] ?? [];
	$logos = $st['logos'] ?? [];
	$heading = $s['title'] ?? ($st['heading'] ?? 'Teams that trust our delivery');
	if (! is_array($logos) || $logos === []) {
		$logos = [
			['src' => asset('theme/images/client/01.svg'), 'alt' => ''],
			['src' => asset('theme/images/client/02.svg'), 'alt' => ''],
			['src' => asset('theme/images/client/03.svg'), 'alt' => ''],
			['src' => asset('theme/images/client/04.svg'), 'alt' => ''],
			['src' => asset('theme/images/client/05.svg'), 'alt' => ''],
		];
	}
@endphp
<section class="pb-0">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-md-3 mb-2 mb-md-0" data-aos="fade-up">
				<h5 class="pe-6 mb-0">{{ $heading }}</h5>
			</div>
			<div class="col-md-9" data-aos="fade-up" data-aos-delay="100">
				<div class="swiper" data-swiper-options='{"slidesPerView":2,"spaceBetween":50,"breakpoints":{"576":{"slidesPerView":3},"992":{"slidesPerView":4},"1200":{"slidesPerView":5}}}'>
					<div class="swiper-wrapper align-items-center">
						@foreach($logos as $logo)
							@php
								$src = is_array($logo) ? ($logo['src'] ?? $logo['url'] ?? '') : (string) $logo;
								$alt = is_array($logo) ? ($logo['alt'] ?? '') : '';
							@endphp
							<div class="swiper-slide">
								<img src="{{ $src }}" class="grayscale" alt="{{ $alt }}" loading="lazy" width="160" height="48" onerror="this.style.display='none'">
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
