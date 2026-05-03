<section class="bg-light">
	<div class="container py-5">
		<div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
			<div data-aos="fade-right">
				<h2 class="mb-1">{{ $home['portfolio_title'] ?? 'Featured work' }}</h2>
				<p class="mb-0 text-muted">{{ $home['portfolio_subtitle'] ?? '' }}</p>
			</div>
			<a href="{{ route('case-studies.index') }}" class="btn btn-dark btn-sm mb-0" data-aos="fade-left">View case studies</a>
		</div>
		<div class="row g-4">
			@forelse($projects as $project)
				<div class="col-md-6 col-lg-4" data-aos="fade-up">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<span class="badge text-bg-primary mb-2">{{ $project->client_name ?: 'Case study' }}</span>
							<h5 class="card-title"><a href="{{ route('case-studies.show', $project->slug) }}" class="stretched-link text-decoration-none">{{ $project->title }}</a></h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($project->summary, 120) }}</p>
						</div>
					</div>
				</div>
			@empty
				<p class="text-muted">Publish featured case studies in the admin to show them here.</p>
			@endforelse
		</div>
	</div>
</section>
