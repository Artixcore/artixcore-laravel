@extends('layouts.app')

@section('meta_title', 'Market & industry updates — '.($site->site_name ?? 'Artixcore'))
@section('meta_description', 'Qualitative market perspectives for SaaS, AI, cloud, and enterprise buyers — verify figures before citing.')

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-2">Market &amp; industry updates</h1>
		<p class="text-muted mb-4">Trend signals and editorial guidance — always confirm statistics against primary sources.</p>
		<form method="get" action="{{ route('market-updates.index') }}" class="row g-3 align-items-end mb-4">
			<div class="col-md-4">
				<label class="form-label small">Market area</label>
				<input type="text" name="market_area" value="{{ $filters['market_area'] ?? '' }}" class="form-control form-control-sm">
			</div>
			<div class="col-md-4">
				<label class="form-label small">Search</label>
				<input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm">
			</div>
			<div class="col-md-4">
				<button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
			</div>
		</form>
		@if(!empty($activeCategory))
			<p class="small text-muted mb-3">Category: <strong>{{ $activeCategory->name }}</strong> · <a href="{{ route('market-updates.index') }}">Clear</a></p>
		@endif
		@if(!empty($activeTag))
			<p class="small text-muted mb-3">Tag: <strong>{{ $activeTag->name }}</strong> · <a href="{{ route('market-updates.index') }}">Clear</a></p>
		@endif
		@if($categoriesNav->isNotEmpty())
			<div class="mb-4 d-flex flex-wrap gap-2">
				@foreach($categoriesNav as $cat)
					<a href="{{ route('market-updates.category', $cat->slug) }}" class="btn btn-sm btn-outline-secondary">{{ $cat->name }}</a>
				@endforeach
			</div>
		@endif
		<div class="row g-4">
			@foreach($marketUpdates as $row)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<img src="{{ $row->main_image_url }}" class="card-img-top object-fit-cover" style="height:160px;" alt="" loading="lazy">
						<div class="card-body">
							@if($row->market_area)<span class="badge bg-info-subtle text-info-emphasis small">{{ $row->market_area }}</span>@endif
							<h5 class="card-title mt-2"><a href="{{ route('market-updates.show', $row->slug) }}" class="stretched-link text-decoration-none">{{ $row->title }}</a></h5>
							<p class="small text-muted">{{ \Illuminate\Support\Str::limit(strip_tags((string) $row->excerpt), 140) }}</p>
							@if($row->published_at)<p class="small text-muted mb-0">{{ $row->published_at->format('M j, Y') }}</p>@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="mt-4">{{ $marketUpdates->links() }}</div>
	</div>
</section>
@endsection
