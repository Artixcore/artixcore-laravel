@extends('layouts.app')

@section('meta_title', 'Portfolio — '.($site->site_name ?? 'Artixcore'))

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-4">Portfolio</h1>
		<div class="row g-4">
			@foreach($projects as $project)
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<h5><a href="{{ route('portfolio.show', $project->slug) }}" class="stretched-link text-decoration-none">{{ $project->title }}</a></h5>
							@if($project->client_name)
								<p class="small text-primary mb-2">{{ $project->client_name }}</p>
							@endif
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($project->summary, 140) }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="mt-4">{{ $projects->links() }}</div>
	</div>
</section>
@endsection
