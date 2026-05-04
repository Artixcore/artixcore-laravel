@php
	$s = $section ?? [];
	$items = $s['items'] ?? [];
	$avatarFallback = asset('theme/images/avatar/01.jpg');
@endphp
@if(count($items) > 0)
<section class="bg-light py-5">
	<div class="container">
		<h2 class="text-center mb-5" data-aos="fade-up">{{ $s['title'] ?? 'What clients say' }}</h2>
		@if(!empty($s['subtitle']))
			<p class="text-center text-muted mb-5">{{ $s['subtitle'] }}</p>
		@endif
		<div class="swiper pb-5" data-swiper-options='{"spaceBetween":30,"pagination":{"el":".swiper-pagination","clickable":true},"breakpoints":{"576":{"slidesPerView":1},"768":{"slidesPerView":2},"1200":{"slidesPerView":3}}}'>
			<div class="swiper-wrapper">
				@foreach($items as $it)
					<div class="swiper-slide" data-aos="fade-up">
						<div class="card card-body shadow-sm h-100 p-4">
							<p class="mb-4">“{{ $it['body'] ?? '' }}”</p>
							<div class="d-flex align-items-center mt-auto">
								<img
									src="{{ $it['image_url'] ?? $avatarFallback }}"
									class="avatar avatar-md rounded-circle"
									alt="{{ $it['author'] ?? '' }}"
									loading="lazy"
									width="48"
									height="48"
									onerror="this.onerror=null;this.src='{{ $avatarFallback }}'"
								>
								<div class="ms-3">
									<h6 class="mb-0">{{ $it['author'] ?? '' }}</h6>
									<small class="text-muted">{{ trim(($it['role'] ?? '').(($it['role'] ?? '') && ($it['company'] ?? '') ? ' · ' : '').($it['company'] ?? '')) }}</small>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="swiper-pagination swiper-pagination-primary position-relative mt-3"></div>
		</div>
	</div>
</section>
@endif
