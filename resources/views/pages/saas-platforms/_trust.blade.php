@php $p = $saasPage; @endphp
@if(!empty($p['show_trust_logos']))
<section class="pt-0 pb-5">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-md-4 col-lg-3 mb-3 mb-md-0" data-aos="fade-up">
				<h5 class="pe-md-4 mb-0">{{ $p['trust_title'] ?? 'Teams that trust our delivery' }}</h5>
			</div>
			<div class="col-md-8 col-lg-9" data-aos="fade-up" data-aos-delay="80">
				<div class="swiper" data-swiper-options='{"slidesPerView":2,"spaceBetween":40,"breakpoints":{"576":{"slidesPerView":3},"992":{"slidesPerView":4},"1200":{"slidesPerView":5}}}'>
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
@endif
