<section class="pb-0">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-md-3 mb-2 mb-md-0" data-aos="fade-up">
				<h5 class="pe-6">{{ $home['clients_heading'] ?? 'Teams that trust our delivery' }}</h5>
			</div>
			<div class="col-md-9" data-aos="fade-up" data-aos-delay="100">
				<div class="swiper" data-swiper-options='{"slidesPerView":2,"spaceBetween":50,"breakpoints":{"576":{"slidesPerView":3},"992":{"slidesPerView":4},"1200":{"slidesPerView":5}}}'>
					<div class="swiper-wrapper align-items-center">
						@foreach(['01','02','03','04','05'] as $c)
							<div class="swiper-slide">
								<img src="{{ asset('theme/images/client/'.$c.'.svg') }}" class="grayscale" alt="">
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
