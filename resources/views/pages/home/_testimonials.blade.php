@if($testimonials->isNotEmpty())
<section class="bg-light py-5">
	<div class="container">
		<h2 class="text-center mb-5" data-aos="fade-up">What clients say</h2>
		<div class="swiper pb-5" data-swiper-options='{"spaceBetween":30,"pagination":{"el":".swiper-pagination","clickable":true},"breakpoints":{"576":{"slidesPerView":1},"768":{"slidesPerView":2},"1200":{"slidesPerView":3}}}'>
			<div class="swiper-wrapper">
				@foreach($testimonials as $t)
					<div class="swiper-slide" data-aos="fade-up">
						<div class="card card-body shadow-sm h-100 p-4">
							<p class="mb-4">“{{ $t->body }}”</p>
							<div class="d-flex align-items-center mt-auto">
								@if($t->avatarMedia)
									<img src="{{ $t->avatarMedia->absoluteUrl() }}" class="avatar avatar-md rounded-circle" alt="">
								@else
									<div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">{{ strtoupper(substr($t->author_name, 0, 1)) }}</div>
								@endif
								<div class="ms-3">
									<h6 class="mb-0">{{ $t->author_name }}</h6>
									<small class="text-muted">{{ trim(($t->role ?: '').($t->role && $t->company ? ' · ' : '').($t->company ?: '')) }}</small>
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
