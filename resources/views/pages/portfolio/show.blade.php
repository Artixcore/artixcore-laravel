@extends('layouts.app')

@php
	$siteName = $site->site_name ?? 'Artixcore';
	$pageTitle = ($item->meta_title ?: $item->title).' — '.$siteName;
	$pageDesc = $item->meta_description ?: $item->short_description ?? '';
	$keywords = $item->meta_keywords ?? '';
	$canonical = $item->canonical_url ?: route('portfolio.show', $item->slug);
	$ogImageRaw = $item->main_image_url;
	$ogImage = str_starts_with($ogImageRaw, 'http') ? $ogImageRaw : url($ogImageRaw);
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 160))
@section('meta_keywords', $keywords)
@section('meta_robots', $item->robots ?: 'index,follow')
@section('canonical_url', $canonical)
@section('og_title', ($item->meta_title ?: $item->title).' — '.$siteName)
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
			['@type' => 'ListItem', 'position' => 2, 'name' => 'Portfolio', 'item' => route('portfolio.index')],
			['@type' => 'ListItem', 'position' => 3, 'name' => $item->title, 'item' => $canonical],
		],
	];
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumb, $jsonFlags) !!}</script>
@if(isset($faqs) && $faqs->isNotEmpty())
	@include('partials.faq-jsonld', ['faqs' => $faqs])
@endif
@endpush

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('portfolio.index') }}">Portfolio</a></li>
				<li class="breadcrumb-item active">{{ $item->title }}</li>
			</ol>
		</nav>
		<h1 class="mb-3">{{ $item->title }}</h1>
		<div class="d-flex flex-wrap gap-3 small text-muted mb-4">
			@if($item->client_name)<span><strong>Client:</strong> {{ $item->client_name }}</span>@endif
			@if($item->project_type)<span><strong>Type:</strong> {{ $item->project_type }}</span>@endif
			@if($item->industry)<span><strong>Industry:</strong> {{ $item->industry }}</span>@endif
		</div>
		<div class="ratio ratio-21x9 rounded overflow-hidden shadow-sm bg-secondary bg-opacity-10 mb-5">
			<img src="{{ $item->main_image_url }}" alt="" class="object-fit-cover w-100 h-100" loading="eager">
		</div>
	</div>
</section>

<section class="pb-6">
	<div class="container prose-lg">
		@if($bodyHtml)
			<h2 class="h4">Overview</h2>
			<div class="mb-5">{!! $bodyHtml !!}</div>
		@endif
		@if($challengeHtml)
			<h2 class="h4">Challenge</h2>
			<div class="mb-5">{!! $challengeHtml !!}</div>
		@endif
		@if($solutionHtml)
			<h2 class="h4">Solution</h2>
			<div class="mb-5">{!! $solutionHtml !!}</div>
		@endif
		@php $tech = $item->technology_stack; @endphp
		@if(is_array($tech) && $tech !== [])
			<h2 class="h4">Technology</h2>
			<ul class="mb-5">
				@foreach($tech as $t)
					<li>{{ $t }}</li>
				@endforeach
			</ul>
		@endif
		@if($item->outcome)
			<h2 class="h4">Outcome</h2>
			<p class="mb-5">{{ $item->outcome }}</p>
		@endif
		@if(is_array($videoEmbed ?? null) && !empty($videoEmbed['embed_url']))
			<div class="ratio ratio-16x9 mb-5">
				<iframe src="{{ $videoEmbed['embed_url'] }}" title="Video" allowfullscreen class="rounded border-0"></iframe>
			</div>
		@endif
	</div>
</section>

@php $bundle = $bundle ?? []; @endphp
@if(!empty($bundle['services']) && $bundle['services']->isNotEmpty())
<section class="py-6 bg-light bg-opacity-50 border-top border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">Related services</h2>
		<ul class="list-unstyled row g-3">
			@foreach($bundle['services'] as $svc)
				<li class="col-md-6">
					<a href="{{ route('services.show', $svc->slug) }}" class="fw-semibold text-decoration-none">{{ $svc->title }}</a>
				</li>
			@endforeach
		</ul>
	</div>
</section>
@endif

@if(!empty($bundle['articles']) && $bundle['articles']->isNotEmpty())
<section class="py-6 border-bottom">
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
<section class="py-6 bg-light bg-opacity-25 border-bottom">
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
<section class="py-6 border-bottom">
	<div class="container">
		<h2 class="h4 mb-4">Testimonials</h2>
		<div class="row g-4">
			@foreach($testimonials as $tm)
				<div class="col-md-6">
					<blockquote class="card border-0 shadow-sm h-100 mb-0">
						<div class="card-body">
							<p class="mb-3">{{ $tm->body }}</p>
							<footer class="small text-muted"><strong>{{ $tm->author_name }}</strong></footer>
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
		<div class="accordion" id="pfFaq">
			@foreach($faqs as $i => $faq)
				<div class="accordion-item">
					<h3 class="accordion-header">
						<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#pfFaq{{ $faq->id }}">{{ $faq->question }}</button>
					</h3>
					<div id="pfFaq{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#pfFaq">
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
		<a href="{{ route('lead.create') }}" class="btn btn-primary btn-lg">Discuss your project</a>
	</div>
</section>
@endsection
