@php
	$s = $section ?? [];
	$st = $s['settings'] ?? [];
	$title = trim((string) ($s['title'] ?? ''));
	if ($title === '') {
		$title = 'Artixcore builds secure SaaS platforms, AI systems, and scalable digital products.';
	}
	$subtitle = trim((string) ($s['subtitle'] ?? ''));
	$heroImage = $s['image_url'] ?? asset('theme/images/bg/01.jpg');
	$trustLine = trim((string) ($st['hero_trust_line'] ?? 'Laravel, React, AI, cloud infrastructure, automation, and product engineering under one roof.'));
	$statValue = (string) ($st['hero_stat_value'] ?? '5K+');
	$statLabel = (string) ($st['hero_stat_label'] ?? 'Active users across client products');
	$primaryLabel = (string) ($s['button_text'] ?? 'Start a Project');
	$primaryUrl = (string) ($s['button_url'] ?? '/lead');
	$secLabel = (string) ($s['secondary_button_text'] ?? 'Explore Services');
	$secUrl = (string) ($s['secondary_button_url'] ?? '/services');
@endphp
@push('styles')
<style>
	.artix-hero-copy { max-width: 42rem; }
	.artix-hero-title {
		font-size: clamp(1.65rem, 2.8vw + 1rem, 2.85rem);
		font-weight: 700;
		line-height: 1.18;
		overflow-wrap: anywhere;
		word-break: break-word;
	}
	.artix-hero-subtitle {
		font-size: clamp(1rem, 0.35vw + 0.95rem, 1.125rem);
		line-height: 1.55;
		overflow-wrap: anywhere;
		word-break: break-word;
	}
	.artix-hero-trust {
		font-size: 0.9375rem;
		line-height: 1.5;
		overflow-wrap: anywhere;
	}
	.artix-hero-visual img.artix-hero-photo {
		width: 100%;
		height: auto;
		aspect-ratio: 4 / 3;
		object-fit: cover;
	}
</style>
@endpush
<section class="position-relative overflow-hidden pb-0 pt-xl-9">
	<div class="position-absolute top-0 start-0 ms-n7 d-none d-xl-block" aria-hidden="true">
		<img src="{{ asset('theme/images/elements/decoration-pattern.svg') }}" class="opacity-50" alt="" loading="lazy" width="200" height="200">
	</div>
	<div class="container pt-4 pt-sm-5">
		<div class="row g-xl-5 align-items-start">
			<div class="col-xl-7 mb-5 mb-xl-0" data-aos="fade-right">
				<div class="pe-xxl-4 artix-hero-copy">
					@if(!empty($s['badge_text']))
						<span class="heading-color d-inline-block bg-light small rounded-3 px-3 py-2">{{ $s['badge_text'] }}</span>
					@endif
					<h1 class="artix-hero-title mt-3 mb-0 text-dark">{{ $title }}</h1>
					@if($subtitle !== '')
						<p class="artix-hero-subtitle mb-0 mt-4 mt-md-4 text-body">{{ $subtitle }}</p>
					@endif
					@if(!empty($s['description']))
						<p class="artix-hero-subtitle mb-0 mt-3 text-body">{{ $s['description'] }}</p>
					@endif
					@if($trustLine !== '')
						<p class="artix-hero-trust text-muted mb-0 mt-3">{{ $trustLine }}</p>
					@endif
					<div class="d-flex flex-wrap gap-2 mt-4 mt-md-5">
						<a class="btn btn-primary mb-0" href="{{ url($primaryUrl) }}">{{ $primaryLabel }}</a>
						<a class="btn btn-light mb-0" href="{{ url($secUrl) }}">{{ $secLabel }}</a>
					</div>
				</div>
			</div>
			<div class="col-md-10 col-xl-5 position-relative mx-auto mt-7 mt-xl-0 artix-hero-visual" data-aos="fade-left">
				<img
					src="{{ $heroImage }}"
					class="rounded artix-hero-photo"
					alt="{{ $site->site_name ?? 'Artixcore' }} — product engineering"
					width="776"
					height="582"
					fetchpriority="high"
					decoding="async"
					onerror="this.onerror=null;this.src='{{ asset('theme/images/bg/01.jpg') }}'"
				>
				<div class="d-inline-block bg-dark rounded-4 position-absolute start-0 bottom-0 mb-md-4 ms-md-n5 p-3">
					<div class="d-flex align-items-center">
						<h6 class="text-white mb-0 me-2">{{ $statValue }}</h6>
					</div>
					<p class="text-white mb-0 mt-2 small">{{ $statLabel }}</p>
				</div>
			</div>
		</div>
	</div>
</section>
