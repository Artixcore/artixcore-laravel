@php $p = $saasPage; @endphp
@if(!empty($p['show_case_studies']) && $caseStudies->isNotEmpty())
<section class="py-5 bg-light">
	<div class="container">
		<div class="inner-container text-center mb-4 mb-sm-6" data-aos="fade-up">
			<h2 class="mb-0">{{ $p['case_studies_title'] ?? 'Featured work' }}</h2>
			@if(!empty($p['case_studies_subtitle']))
				<p class="mb-0 mt-3 text-muted">{{ $p['case_studies_subtitle'] }}</p>
			@endif
		</div>
		<div class="row g-4">
			@foreach($caseStudies as $i => $project)
				<div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ min($i * 60, 240) }}">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body d-flex flex-column">
							<h5><a href="{{ route('portfolio.show', $project->slug) }}" class="stretched-link text-decoration-none">{{ $project->title }}</a></h5>
							@if($project->client_name)
								<p class="small text-primary mb-2">{{ $project->client_name }}</p>
							@endif
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($project->summary, 160) }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="text-center mt-4" data-aos="fade-up">
			<a href="{{ route('portfolio.index') }}" class="btn btn-outline-primary">View full portfolio</a>
		</div>
	</div>
</section>
@endif
