@extends('layouts.app')

@section('meta_title', 'Services — '.($site->site_name ?? 'Artixcore'))

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-4">Services</h1>
		<div class="row g-4">
			@foreach($services as $service)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<div class="icon-md text-primary mb-3"><i class="{{ $service->icon ?: 'bi bi-grid' }}"></i></div>
							<h5><a href="{{ route('services.show', $service->slug) }}" class="stretched-link text-decoration-none">{{ $service->title }}</a></h5>
							<p class="small text-muted mb-0">{{ $service->summary }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endsection
