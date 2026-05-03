@extends('layouts.app')

@php
	$siteName = $site->site_name ?? 'Artixcore';
	$pageTitle = ($service->meta_title ?: $service->title).' — '.$siteName;
	$pageDesc = $service->meta_description ?: $service->summary ?? '';
	$keywords = $service->meta_keywords ?? '';
	$canonical = $service->canonical_url ?: route('services.show', $service->slug);
	$ogImageRaw = $service->main_image_url;
	$ogImage = str_starts_with($ogImageRaw, 'http') ? $ogImageRaw : url($ogImageRaw);
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 160))
@section('meta_keywords', $keywords)
@section('meta_robots', $service->robots ?: 'index,follow')
@section('canonical_url', $canonical)
@section('og_title', ($service->meta_title ?: $service->title).' — '.$siteName)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 200))
@section('og_image', $ogImage)

@push('jsonld')
@php
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	$serviceLd = [
		'@context' => 'https://schema.org',
		'@type' => 'Service',
		'name' => $service->title,
		'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($service->summary ?? '')), 300),
		'provider' => ['@type' => 'Organization', 'name' => $siteName],
	];
	$breadcrumb = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => [
			['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
			['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => route('services.index')],
			['@type' => 'ListItem', 'position' => 3, 'name' => $service->title, 'item' => $canonical],
		],
	];
@endphp
<script type="application/ld+json">{!! json_encode($serviceLd, $jsonFlags) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, $jsonFlags) !!}</script>
@if(isset($faqs) && $faqs->isNotEmpty())
	@include('partials.faq-jsonld', ['faqs' => $faqs])
@endif
@endpush

@section('content')
<section class="pt-8 pb-5 bg-light bg-opacity-25 border-bottom">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
				<li class="breadcrumb-item active">{{ $service->title }}</li>
			</ol>
		</nav>
		<div class="row align-items-center g-4">
			<div class="col-lg-8">
				@if($service->featured)
					<span class="badge bg-primary-subtle text-primary-emphasis mb-2">Featured</span>
				@endif
				<h1 class="mb-3">{{ $service->title }}</h1>
				@if($service->summary)
					<p class="lead text-muted">{{ $service->summary }}</p>
				@endif
			</div>
			<div class="col-lg-4">
				<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm bg-secondary bg-opacity-10">
					<img src="{{ $service->main_image_url }}" alt="" class="object-fit-cover" loading="eager" width="640" height="360">
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-6">
	<div class="container prose-lg">
		<h2 class="h4">Overview</h2>
		@if($bodyHtml)
			<div class="mb-5">{!! $bodyHtml !!}</div>
		@endif

		@php $benefitsList = $benefitsList ?? []; @endphp
		@if($benefitsList !== [])
			<h2 class="h4">Benefits</h2>
			<ul class="mb-5">
				@foreach($benefitsList as $b)
					@if(is_array($b))
						<li><strong>{{ $b['title'] ?? '' }}</strong> @if(!empty($b['body'])) — {{ $b['body'] }} @endif</li>
					@else
						<li>{{ $b }}</li>
					@endif
				@endforeach
			</ul>
		@endif

		@php $processList = $processList ?? []; @endphp
		@if($processList !== [])
			<h2 class="h4">Process</h2>
			<ol class="mb-5">
				@foreach($processList as $step)
					@if(is_array($step))
						<li><strong>{{ $step['title'] ?? '' }}</strong> @if(!empty($step['body'])) — {{ $step['body'] }} @endif</li>
					@else
						<li>{{ $step }}</li>
					@endif
				@endforeach
			</ol>
		@endif

		@php $technologiesList = $technologiesList ?? []; @endphp
		@if($technologiesList !== [])
			<h2 class="h4">Technologies</h2>
			<ul class="mb-5">
				@foreach($technologiesList as $t)
					<li>{{ is_array($t) ? ($t['name'] ?? '') : $t }}</li>
				@endforeach
			</ul>
		@endif
	</div>
</section>

@php $bundle = $bundle ?? []; @endphp
@if(!empty($bundle['articles']) && $bundle['articles']->isNotEmpty())
<section class="py-6 bg-light bg-opacity-50 border-top border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">Related articles</h2>
		<div class="row g-4">
			@foreach($bundle['articles'] as $art)
				<div class="col-md-6 col-lg-4">
					<a href="{{ route('articles.show', $art->slug) }}" class="text-reset text-decoration-none card border-0 shadow-sm h-100 overflow-hidden">
						<div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
							<img src="{{ $art->main_image_url }}" alt="" class="object-fit-cover" loading="lazy" width="400" height="225">
						</div>
						<div class="card-body">
							<h3 class="h6 mb-0">{{ $art->title }}</h3>
						</div>
					</a>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@if(!empty($bundle['caseStudies']) && $bundle['caseStudies']->isNotEmpty())
<section class="py-6 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">Case studies</h2>
		<div class="row g-4">
			@foreach($bundle['caseStudies'] as $cs)
				<div class="col-md-6 col-lg-4">
					<a href="{{ route('case-studies.show', $cs->slug) }}" class="stretched-link text-decoration-none card border-0 shadow-sm h-100">
						<div class="card-body">
							<h3 class="h6">{{ $cs->title }}</h3>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($cs->outcome_summary ?? '', 120) }}</p>
						</div>
					</a>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@if(!empty($bundle['portfolioItems']) && $bundle['portfolioItems']->isNotEmpty())
<section class="py-6 bg-light bg-opacity-50 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">Portfolio</h2>
		<div class="row g-4">
			@foreach($bundle['portfolioItems'] as $pf)
				<div class="col-md-6 col-lg-4">
					<a href="{{ route('portfolio.show', $pf->slug) }}" class="text-reset text-decoration-none card border-0 shadow-sm h-100 overflow-hidden">
						<div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
							<img src="{{ $pf->main_image_url }}" alt="" class="object-fit-cover" loading="lazy">
						</div>
						<div class="card-body">
							<h3 class="h6 mb-0">{{ $pf->title }}</h3>
						</div>
					</a>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@php $testimonials = $testimonials ?? collect(); @endphp
@if($testimonials->isNotEmpty())
<section class="py-6 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">What clients say</h2>
		<div class="row g-4">
			@foreach($testimonials as $tm)
				<div class="col-md-6">
					<blockquote class="card border-0 shadow-sm h-100 mb-0">
						<div class="card-body">
							<p class="mb-3">{{ $tm->body }}</p>
							<footer class="small text-muted">
								<strong>{{ $tm->author_name }}</strong>
								@if($tm->role) · {{ $tm->role }} @endif
								@if($tm->company) · {{ $tm->company }} @endif
							</footer>
						</div>
					</blockquote>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@php $faqs = $faqs ?? collect(); @endphp
@if($faqs->isNotEmpty())
<section class="py-6 bg-light bg-opacity-25 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">FAQs</h2>
		<div class="accordion" id="svcFaq">
			@foreach($faqs as $i => $faq)
				<div class="accordion-item">
					<h3 class="accordion-header">
						<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#svcFaq{{ $faq->id }}">{{ $faq->question }}</button>
					</h3>
					<div id="svcFaq{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#svcFaq">
						<div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

<section class="py-6">
	<div class="container text-center">
		<h2 class="h5 mb-3">Discuss {{ $service->title }} with our team</h2>
		<a href="{{ route('lead.create') }}" class="btn btn-primary btn-lg">Get in touch</a>
	</div>
</section>
@endsection
