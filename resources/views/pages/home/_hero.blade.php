@php
	$h = $home;
	$typed = isset($h['hero_typed_phrases']) && is_array($h['hero_typed_phrases'])
		? implode('&&', $h['hero_typed_phrases'])
		: 'SaaS platforms&&AI systems&&Web3 products';
@endphp
<section class="position-relative overflow-hidden pb-0 pt-xl-9">
	<div class="position-absolute top-0 start-0 ms-n7 d-none d-xl-block">
		<img src="{{ asset('theme/images/elements/decoration-pattern.svg') }}" alt="">
	</div>
	<div class="container pt-4 pt-sm-5">
		<div class="row g-xl-5">
			<div class="col-xl-7 mb-5 mb-xl-0" data-aos="fade-right">
				<div class="pe-xxl-4">
					@if(!empty($h['hero_badge']))
						<span class="heading-color d-inline-block bg-light small rounded-3 px-3 py-2">{{ $h['hero_badge'] }}</span>
					@endif
					<h1 class="mt-3 lh-base">
						{{ $h['hero_title_prefix'] ?? 'We build' }}
						<span class="cd-headline clip big-clip is-full-width text-primary mb-0 d-block d-xxl-inline-block">
							<span class="typed" data-type-text="{{ e($typed) }}"></span>
						</span>
					</h1>
					<p class="mb-0 mt-4 mt-md-5">{{ $h['hero_subtitle'] ?? '' }}</p>
					<div class="d-flex flex-wrap gap-2 mt-4 mt-md-5">
						<a class="btn btn-primary mb-0" href="{{ url($h['hero_primary_cta_url'] ?? '/contact') }}">{{ $h['hero_primary_cta_label'] ?? 'Start a project' }}</a>
						<a class="btn btn-light mb-0" href="{{ url($h['hero_secondary_cta_url'] ?? '/services') }}">{{ $h['hero_secondary_cta_label'] ?? 'Services' }}</a>
					</div>
				</div>
			</div>
			<div class="col-md-10 col-xl-5 position-relative mx-auto mt-7 mt-xl-0" data-aos="fade-left">
				<img src="{{ asset('theme/images/bg/01.jpg') }}" class="rounded" alt="">
				<div class="d-inline-block bg-dark rounded-4 position-absolute start-0 bottom-0 mb-md-4 ms-md-n5 p-3">
					<div class="d-flex align-items-center">
						<h6 class="text-white mb-0 me-2">{{ $h['hero_stat_value'] ?? '5K+' }}</h6>
					</div>
					<p class="text-white mb-0 mt-2 small">{{ $h['hero_stat_label'] ?? 'Users powered by our client products' }}</p>
				</div>
			</div>
		</div>
	</div>
</section>
