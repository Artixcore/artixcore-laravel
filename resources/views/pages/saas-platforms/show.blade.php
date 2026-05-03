@extends('layouts.app')

@php
	$siteName = $site->site_name ?? 'Artixcore';
	$p = $platform;
	$pageTitle = ($p->meta_title ?: $p->title).' — '.$siteName;
	$pageDesc = $p->meta_description ?: $p->summary ?? '';
	$keywords = $p->meta_keywords ?? '';
	$canonical = $p->canonical_url ?: route('saas-platforms.show', $p->slug);
	$ogImageRaw = $p->main_image_url;
	$ogImage = str_starts_with($ogImageRaw, 'http') ? $ogImageRaw : url($ogImageRaw);
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 160))
@section('meta_keywords', $keywords)
@section('meta_robots', $p->robots ?: 'index,follow')
@section('canonical_url', $canonical)
@section('og_title', ($p->meta_title ?: $p->title).' — '.$siteName)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 200))
@section('og_image', $ogImage)

@push('jsonld')
@php
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	$breadcrumb = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => [
			['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
			['@type' => 'ListItem', 'position' => 2, 'name' => 'SaaS platforms', 'item' => route('saas-platforms')],
			['@type' => 'ListItem', 'position' => 3, 'name' => $p->title, 'item' => $canonical],
		],
	];
@endphp
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
				<li class="breadcrumb-item"><a href="{{ route('saas-platforms') }}">SaaS platforms</a></li>
				<li class="breadcrumb-item active">{{ $p->title }}</li>
			</ol>
		</nav>
		<div class="row g-4 align-items-center">
			<div class="col-lg-8">
				@if($p->platform_type)
					<span class="badge bg-secondary-subtle text-secondary-emphasis mb-2">{{ $p->platform_type }}</span>
				@endif
				<h1 class="mb-2">{{ $p->title }}</h1>
				@if($p->tagline)
					<p class="lead text-muted mb-0">{{ $p->tagline }}</p>
				@elseif($p->summary)
					<p class="lead text-muted mb-0">{{ $p->summary }}</p>
				@endif
			</div>
			<div class="col-lg-4">
				<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm bg-secondary bg-opacity-10">
					<img src="{{ $p->main_image_url }}" alt="" class="object-fit-cover" loading="eager">
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

		@php $featuresList = $featuresList ?? []; @endphp
		@if($featuresList !== [])
			<h2 class="h4">Features</h2>
			<ul class="mb-5">
				@foreach($featuresList as $f)
					@if(is_array($f))
						<li><strong>{{ $f['title'] ?? '' }}</strong> @if(!empty($f['body'])) — {{ $f['body'] }} @endif</li>
					@else
						<li>{{ $f }}</li>
					@endif
				@endforeach
			</ul>
		@endif

		@php $useCasesList = $useCasesList ?? []; @endphp
		@if($useCasesList !== [])
			<h2 class="h4">Use cases</h2>
			<ul class="mb-5">
				@foreach($useCasesList as $u)
					@if(is_array($u))
						<li><strong>{{ $u['title'] ?? '' }}</strong> @if(!empty($u['body'])) — {{ $u['body'] }} @endif</li>
					@else
						<li>{{ $u }}</li>
					@endif
				@endforeach
			</ul>
		@endif

		@if($p->target_audience)
			<h2 class="h4">Who it’s for</h2>
			<p class="mb-5">{{ $p->target_audience }}</p>
		@endif

		@if($p->pricing_note)
			<h2 class="h4">Pricing</h2>
			<p class="mb-5">{{ $p->pricing_note }}</p>
		@endif

		@if(is_array($videoEmbed ?? null) && !empty($videoEmbed['embed_url']))
			<div class="ratio ratio-16x9 mb-5">
				<iframe src="{{ $videoEmbed['embed_url'] }}" title="Platform video" allowfullscreen class="rounded border-0"></iframe>
			</div>
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
							<img src="{{ $art->main_image_url }}" alt="" class="object-fit-cover" loading="lazy">
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
		<h2 class="h4 mb-4">Related case studies</h2>
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

@php $testimonials = $testimonials ?? collect(); @endphp
@if($testimonials->isNotEmpty())
<section class="py-6 bg-light bg-opacity-25 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">Testimonials</h2>
		<div class="row g-4">
			@foreach($testimonials as $tm)
				<div class="col-md-6">
					<blockquote class="card border-0 shadow-sm h-100 mb-0">
						<div class="card-body">
							<p class="mb-3">{{ $tm->body }}</p>
							<footer class="small text-muted"><strong>{{ $tm->author_name }}</strong> @if($tm->company) · {{ $tm->company }} @endif</footer>
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
<section class="py-6 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">FAQs</h2>
		<div class="accordion" id="platFaq">
			@foreach($faqs as $i => $faq)
				<div class="accordion-item">
					<h3 class="accordion-header">
						<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#platFaq{{ $faq->id }}">{{ $faq->question }}</button>
					</h3>
					<div id="platFaq{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#platFaq">
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
		<h2 class="h5 mb-3">Talk to us about {{ $p->title }}</h2>
		<a href="{{ route('lead.create') }}" class="btn btn-primary btn-lg">Start a project</a>
	</div>
</section>
@endsection
