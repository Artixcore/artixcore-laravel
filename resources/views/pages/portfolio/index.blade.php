@extends('layouts.app')

@section('meta_title', 'Portfolio — '.($site->site_name ?? 'Artixcore'))
@section('meta_description', 'Selected projects and implementations from Artixcore.')

@section('content')
<section class="pt-8 pb-6">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item active">Portfolio</li>
			</ol>
		</nav>
		<h1 class="mb-3">Portfolio</h1>
		<p class="lead text-muted">Explore projects across services and industries. Detail pages link related articles and case studies.</p>
	</div>
</section>

<section class="pb-8">
	<div class="container">
		<div class="row g-4">
			@forelse($portfolioItems as $item)
				<div class="col-md-6 col-lg-4">
					<a href="{{ route('portfolio.show', $item->slug) }}" class="text-reset text-decoration-none card border-0 shadow-sm h-100 overflow-hidden">
						<div class="ratio ratio-16x9 bg-secondary bg-opacity-10">
							<img src="{{ $item->main_image_url }}" alt="" class="object-fit-cover" loading="lazy">
						</div>
						<div class="card-body">
							<h2 class="h6 mb-1">{{ $item->title }}</h2>
							@if($item->short_description)
								<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($item->short_description, 120) }}</p>
							@endif
						</div>
					</a>
				</div>
			@empty
				<p class="text-muted">No portfolio projects published yet.</p>
			@endforelse
		</div>
		<div class="mt-5">{{ $portfolioItems->links() }}</div>
	</div>
</section>
@endsection
