@php
	$siteName = $site->site_name ?? 'Artixcore';
@endphp
<section class="pt-8 pb-6">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('case-studies.index') }}">Case studies</a></li>
				<li class="breadcrumb-item active">{{ $study->title }}</li>
			</ol>
		</nav>
		<span class="badge bg-secondary-subtle text-secondary-emphasis mb-2">{{ $study->typeLabel() }}</span>
		<h1 class="mb-3">{{ $study->title }}</h1>
		@if($study->summary)
			<p class="lead text-muted">{{ $study->summary }}</p>
		@endif
		<div class="d-flex flex-wrap gap-3 small text-muted mb-4">
			@if($study->industry)<span><strong>Industry:</strong> {{ $study->industry }}</span>@endif
			@if($study->project_type)<span><strong>Project:</strong> {{ $study->project_type }}</span>@endif
			@if($study->client_display_name || $study->client_name)
				<span><strong>Client / label:</strong> {{ $study->client_display_name ?: $study->client_name }}</span>
			@endif
			@if($study->published_at)<span><strong>Published:</strong> {{ $study->published_at->format('M j, Y') }}</span>@endif
			@if($study->author_name)<span><strong>Author:</strong> {{ $study->author_name }}</span>@endif
		</div>
		@if($study->terms->isNotEmpty())
			<div class="mb-4">
				@foreach($study->terms as $term)
					@if(($term->taxonomy?->slug ?? '') === 'categories')
						<a href="{{ route('case-studies.category', $term->slug) }}" class="badge text-bg-light border me-1">{{ $term->name }}</a>
					@elseif(($term->taxonomy?->slug ?? '') === 'tags')
						<a href="{{ route('case-studies.tag', $term->slug) }}" class="badge text-bg-primary-subtle text-primary-emphasis me-1">{{ $term->name }}</a>
					@endif
				@endforeach
			</div>
		@endif
		<img src="{{ $study->main_image_url }}" alt="" class="img-fluid rounded shadow-sm mb-5 w-100 object-fit-cover" style="max-height:420px;" loading="lazy">
	</div>
</section>
<section class="pb-4">
	<div class="container prose-lg">
		<h2 class="h4">Overview</h2>
		@if($bodyHtml)
			<div class="mb-5">{!! $bodyHtml !!}</div>
		@endif
		@if($challengeHtml)
			<h2 class="h4">The challenge</h2>
			<div class="mb-5">{!! $challengeHtml !!}</div>
		@endif
		@if($solutionHtml)
			<h2 class="h4">The solution</h2>
			<div class="mb-5">{!! $solutionHtml !!}</div>
		@endif
		@if($implementationHtml)
			<h2 class="h4">Architecture &amp; implementation</h2>
			<div class="mb-5">{!! $implementationHtml !!}</div>
		@endif
		@php $tech = $study->technology_stack; @endphp
		@if(is_array($tech) && $tech !== [])
			<h2 class="h4">Technology used</h2>
			<ul class="mb-5">
				@foreach($tech as $item)
					<li>{{ $item }}</li>
				@endforeach
			</ul>
		@endif
		@php $outs = $study->outcomes; @endphp
		@if(is_array($outs) && $outs !== [])
			<h2 class="h4">Results</h2>
			<ul class="mb-5">
				@foreach($outs as $o)
					<li>{{ $o }}</li>
				@endforeach
			</ul>
		@endif
		@php $met = $study->metrics; @endphp
		@if(is_array($met) && $met !== [])
			<h2 class="h4">Metrics (verify before citing)</h2>
			<ul class="mb-5">
				@foreach($met as $m)
					<li><strong>{{ $m['label'] ?? 'Metric' }}</strong> — {{ $m['note'] ?? '' }}</li>
				@endforeach
			</ul>
		@endif
		@if($lessonsHtml)
			<h2 class="h4">Lessons learned</h2>
			<div class="mb-5">{!! $lessonsHtml !!}</div>
		@endif
		@if(!empty($study->gallery_paths) && is_array($study->gallery_paths))
			<div class="row g-3 mb-5">
				@foreach($study->gallery_paths as $url)
					<div class="col-md-4"><img src="{{ $url }}" class="img-fluid rounded" alt="" loading="lazy"></div>
				@endforeach
			</div>
		@endif
		@if(is_array($videoEmbed ?? null) && !empty($videoEmbed['embed_url']))
			<div class="ratio ratio-16x9 mb-5">
				<iframe src="{{ $videoEmbed['embed_url'] }}" title="Video" allowfullscreen class="rounded border-0"></iframe>
			</div>
		@endif
	</div>
</section>

@php $caseStudyBundle = $caseStudyBundle ?? []; @endphp
@if(!empty($caseStudyBundle['services']) && $caseStudyBundle['services']->isNotEmpty())
<section class="pb-6 bg-light bg-opacity-50 border-top">
	<div class="container">
		<h2 class="h4 mb-3">Related services</h2>
		<ul class="list-unstyled row g-2">
			@foreach($caseStudyBundle['services'] as $svc)
				<li class="col-md-6"><a href="{{ route('services.show', $svc->slug) }}" class="fw-semibold text-decoration-none">{{ $svc->title }}</a></li>
			@endforeach
		</ul>
	</div>
</section>
@endif

@if(!empty($caseStudyBundle['platforms']) && $caseStudyBundle['platforms']->isNotEmpty())
<section class="pb-6 border-top">
	<div class="container">
		<h2 class="h4 mb-3">Related SaaS platforms</h2>
		<ul class="list-unstyled row g-2">
			@foreach($caseStudyBundle['platforms'] as $plat)
				<li class="col-md-6"><a href="{{ route('saas-platforms.show', $plat->slug) }}" class="fw-semibold text-decoration-none">{{ $plat->title }}</a></li>
			@endforeach
		</ul>
	</div>
</section>
@endif

@if(!empty($caseStudyBundle['portfolioItems']) && $caseStudyBundle['portfolioItems']->isNotEmpty())
<section class="pb-6 bg-light bg-opacity-25 border-top">
	<div class="container">
		<h2 class="h4 mb-3">Portfolio</h2>
		<ul class="list-unstyled row g-2">
			@foreach($caseStudyBundle['portfolioItems'] as $pf)
				<li class="col-md-6"><a href="{{ route('portfolio.show', $pf->slug) }}" class="fw-semibold text-decoration-none">{{ $pf->title }}</a></li>
			@endforeach
		</ul>
	</div>
</section>
@endif

@if(!empty($caseStudyBundle['articles']) && $caseStudyBundle['articles']->isNotEmpty())
<section class="pb-6 border-top">
	<div class="container">
		<h2 class="h4 mb-4">Related articles</h2>
		<div class="row g-4">
			@foreach($caseStudyBundle['articles'] as $art)
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

@php $studyTestimonials = $studyTestimonials ?? collect(); @endphp
@if($studyTestimonials->isNotEmpty())
<section class="pb-6 bg-light bg-opacity-50 border-top">
	<div class="container">
		<h2 class="h4 mb-4">Testimonials</h2>
		<div class="row g-4">
			@foreach($studyTestimonials as $tm)
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

@php $studyFaqs = $studyFaqs ?? collect(); @endphp
@if($studyFaqs->isNotEmpty())
<section class="pb-6 border-top">
	<div class="container">
		<h2 class="h4 mb-4">FAQs</h2>
		<div class="accordion" id="csFaq">
			@foreach($studyFaqs as $i => $faq)
				<div class="accordion-item">
					<h3 class="accordion-header">
						<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#csFaq{{ $faq->id }}">{{ $faq->question }}</button>
					</h3>
					<div id="csFaq{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#csFaq">
						<div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif

@if(isset($relatedCaseStudies) && $relatedCaseStudies->isNotEmpty())
<section class="pb-6 bg-light">
	<div class="container">
		<h2 class="h4 mb-4">Related case studies</h2>
		<div class="row g-4">
			@foreach($relatedCaseStudies as $rel)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<h5 class="card-title"><a href="{{ route('case-studies.show', $rel->slug) }}" class="stretched-link text-decoration-none">{{ $rel->title }}</a></h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($rel->outcome_summary, 120) }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
@include('partials.lead-cta', [
	'ctaTitle' => 'Next steps',
	'ctaBody' => 'Ready to build something similar with '.$siteName.'?',
	'ctaLabel' => 'Start a project',
])
