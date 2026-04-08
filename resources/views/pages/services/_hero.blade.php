@php $sp = $servicesPage; @endphp
<section class="pt-xl-8 pb-0">
	<div class="container pt-2 pt-sm-4">
		<nav aria-label="breadcrumb" class="mb-3" data-aos="fade-up">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item active" aria-current="page">Services</li>
			</ol>
		</nav>
		<div class="row g-4 g-xxl-5 align-items-center">
			<div class="col-xl-6" data-aos="fade-right">
				@if(!empty($sp['hero_badge']))
					<span class="heading-color d-inline-block bg-light small rounded-3 px-3 py-2">{{ $sp['hero_badge'] }}</span>
				@endif
				<h1 class="mb-0 lh-base mt-3">{{ $sp['hero_title'] ?? 'Services' }}</h1>
				@if(!empty($sp['hero_subtitle']))
					<p class="mb-0 mt-4 mt-xl-5">{{ $sp['hero_subtitle'] }}</p>
				@endif
				<div class="d-flex gap-1 gap-sm-3 flex-wrap mt-4 mt-xl-5">
					@if(!empty($sp['hero_primary_cta_label']))
						<a class="btn btn-primary mb-0" href="{{ url($sp['hero_primary_cta_url'] ?? '/contact') }}">{{ $sp['hero_primary_cta_label'] }}</a>
					@endif
					@if(!empty($sp['hero_secondary_cta_label']))
						<a class="btn btn-outline-primary mb-0" href="{{ url($sp['hero_secondary_cta_url'] ?? '/portfolio') }}">{{ $sp['hero_secondary_cta_label'] }}</a>
					@endif
				</div>
			</div>
			<div class="col-xl-6 text-center" data-aos="fade-left">
				<img src="{{ asset('theme/images/elements/hero-finance.svg') }}" class="img-fluid" alt="">
			</div>
		</div>
		<hr class="border-primary opacity-2 mt-sm-6 my-5 d-none d-md-block">
	</div>
</section>
