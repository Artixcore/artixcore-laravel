@php $p = $saasPage; @endphp
<section class="pt-5 pt-xl-7 pb-0">
	<div class="container-fluid pt-3 pt-xl-5">
		<div class="row">
			<div class="col-xxl-11 mx-auto">
				<div class="bg-dark rounded position-relative overflow-hidden px-4 px-sm-6 pt-6 pt-xl-8 mx-2 mx-sm-3 mx-xl-4" data-bs-theme="dark">
					<div class="position-absolute top-0 start-0 opacity-50">
						<img src="{{ asset('theme/images/elements/bg-pattern-4.png') }}" alt="">
					</div>
					<div class="position-absolute bottom-0 start-0 opacity-50">
						<img src="{{ asset('theme/images/elements/bg-pattern-3.png') }}" alt="">
					</div>
					<div class="inner-container-small text-center position-relative mb-4 px-2">
						@if(!empty($p['hero_badge']))
							<h6 class="text-primary fw-normal mb-3">{{ $p['hero_badge'] }}</h6>
						@endif
						<h1 class="mb-3 display-5">{{ $p['hero_title'] ?? 'SaaS platforms' }}</h1>
						@if(!empty($p['hero_subtitle']))
							<p class="mb-3 mb-lg-4 text-opacity-75">{{ $p['hero_subtitle'] }}</p>
						@endif
						<div class="d-flex flex-wrap gap-2 justify-content-center">
							@if(!empty($p['hero_primary_cta_label']))
								<a class="btn btn-lg btn-primary icon-link icon-link-hover mb-0" href="{{ url($p['hero_primary_cta_url'] ?? '/contact') }}">{{ $p['hero_primary_cta_label'] }}<i class="bi bi-chevron-right"></i></a>
							@endif
							@if(!empty($p['hero_secondary_cta_label']))
								<a class="btn btn-lg btn-outline-light mb-0" href="{{ url($p['hero_secondary_cta_url'] ?? '/portfolio') }}">{{ $p['hero_secondary_cta_label'] }}</a>
							@endif
						</div>
					</div>
					<div class="row position-relative mb-n4 mb-md-n7 mb-xl-n9">
						<div class="col-xl-8 mx-auto text-center">
							<img src="{{ asset('theme/images/bg/saas-bg-5.png') }}" class="img-fluid" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
