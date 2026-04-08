@php $h = $home; @endphp
<section>
	<div class="container">
		<div class="row g-4 align-items-xl-center">
			<div class="col-lg-7" data-aos="fade-up">
				<div class="row pe-xl-5">
					<div class="col-sm-6">
						<img src="{{ asset('theme/images/about/02.jpg') }}" class="rounded" alt="">
					</div>
					<div class="col-sm-6">
						<div class="row mb-4">
							<div class="col-sm-6 mb-4 mb-sm-0">
								<div class="bg-dark text-white rounded text-center p-3">
									<span class="h2 text-white">{{ $h['stat_1_value'] ?? '10+' }}</span>
									<p class="mb-0 small">{{ $h['stat_1_label'] ?? 'Years experience' }}</p>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="bg-primary rounded text-center p-3">
									<span class="h2 text-white">{{ $h['stat_2_value'] ?? '120+' }}</span>
									<p class="mb-0 text-white small">{{ $h['stat_2_label'] ?? 'Launches' }}</p>
								</div>
							</div>
						</div>
						<img src="{{ asset('theme/images/about/01.jpg') }}" class="rounded" alt="">
					</div>
				</div>
			</div>
			<div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
				@if(!empty($h['intro_badge']))
					<span class="heading-color bg-light small rounded-3 px-3 py-2">{{ $h['intro_badge'] }}</span>
				@endif
				<h2 class="my-4">{{ $h['intro_title'] ?? '' }}</h2>
				<p class="mb-4">{{ $h['intro_body'] ?? '' }}</p>
				<a href="{{ route('about') }}" class="btn btn-dark mb-0">About Artixcore</a>
			</div>
		</div>
	</div>
</section>
