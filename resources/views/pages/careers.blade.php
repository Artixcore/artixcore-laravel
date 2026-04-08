@extends('layouts.app')

@section('meta_title', 'Careers — '.($site->site_name ?? 'Artixcore'))

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-3">Join Artixcore</h1>
		<p class="lead text-muted mb-5">Open roles appear below when published from the admin panel.</p>
		<div class="row g-4">
			@forelse($jobs as $job)
				<div class="col-12">
					<div class="card border-0 shadow-sm">
						<div class="card-body p-4">
							<div class="d-flex flex-wrap justify-content-between gap-2">
								<h5 class="mb-0">{{ $job->title }}</h5>
								<div class="small text-muted">
									@if($job->location){{ $job->location }}@endif
									@if($job->location && $job->employment_type) · @endif
									@if($job->employment_type){{ $job->employment_type }}@endif
								</div>
							</div>
							@if($job->body)
								<div class="mt-3 prose-lg">{!! $job->body !!}</div>
							@endif
						</div>
					</div>
				</div>
			@empty
				<p class="text-muted">There are no open listings right now. Check back soon or send your profile via <a href="{{ route('contact') }}">contact</a>.</p>
			@endforelse
		</div>
	</div>
</section>
@endsection
