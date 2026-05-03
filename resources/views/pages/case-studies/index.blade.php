@extends('layouts.app')

@section('meta_title', 'Case studies — '.($site->site_name ?? 'Artixcore'))
@section('meta_description', 'Explore engineering case studies, outcomes, and technology stacks from '.($site->site_name ?? 'Artixcore').'.')

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-2">Case studies</h1>
		<p class="text-muted mb-4">Transformation stories, architecture notes, and measurable outcomes.</p>
		<form method="get" action="{{ route('case-studies.index') }}" class="row g-3 align-items-end mb-4">
			<div class="col-md-3">
				<label class="form-label small">Industry</label>
				<input type="text" name="industry" value="{{ $filters['industry'] ?? '' }}" class="form-control form-control-sm">
			</div>
			<div class="col-md-3">
				<label class="form-label small">Technology contains</label>
				<input type="text" name="technology" value="{{ $filters['technology'] ?? '' }}" class="form-control form-control-sm" placeholder="e.g. Laravel">
			</div>
			<div class="col-md-3">
				<label class="form-label small">Search</label>
				<input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm">
			</div>
			<div class="col-md-3">
				<button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
			</div>
		</form>
		@if(!empty($activeCategory))
			<p class="small text-muted mb-3">Category: <strong>{{ $activeCategory->name }}</strong> · <a href="{{ route('case-studies.index') }}">Clear</a></p>
		@endif
		@if(!empty($activeTag))
			<p class="small text-muted mb-3">Tag: <strong>{{ $activeTag->name }}</strong> · <a href="{{ route('case-studies.index') }}">Clear</a></p>
		@endif
		@if($categoriesNav->isNotEmpty())
			<div class="mb-4 d-flex flex-wrap gap-2">
				@foreach($categoriesNav as $cat)
					<a href="{{ route('case-studies.category', $cat->slug) }}" class="btn btn-sm btn-outline-secondary">{{ $cat->name }}</a>
				@endforeach
			</div>
		@endif
		<div class="row g-4">
			@foreach($caseStudies as $cs)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<img src="{{ $cs->main_image_url }}" class="card-img-top object-fit-cover" style="height:180px;" alt="" loading="lazy">
						<div class="card-body">
							<span class="badge bg-secondary-subtle text-secondary-emphasis small">{{ $cs->typeLabel() }}</span>
							<h5 class="card-title mt-2"><a href="{{ route('case-studies.show', $cs->slug) }}" class="stretched-link text-decoration-none">{{ $cs->title }}</a></h5>
							@if($cs->industry)<p class="small text-primary mb-1">{{ $cs->industry }}</p>@endif
							<p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($cs->outcome_summary, 140) }}</p>
							@php $tech = $cs->technology_stack; @endphp
							@if(is_array($tech) && $tech !== [])
								<p class="small mb-0"><strong>Stack:</strong> {{ implode(', ', array_slice($tech, 0, 4)) }}@if(count($tech) > 4)…@endif</p>
							@endif
							@if($cs->published_at)<p class="small text-muted mt-2 mb-0">{{ $cs->published_at->format('M j, Y') }}</p>@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="mt-4">{{ $caseStudies->links() }}</div>
	</div>
</section>
@endsection
