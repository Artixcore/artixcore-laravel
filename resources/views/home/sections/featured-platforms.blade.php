@php
	$s = $section ?? [];
	$items = $s['items'] ?? [];
@endphp
@if(count($items) > 0)
<section class="py-5">
	<div class="container">
		<div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
			<div data-aos="fade-right">
				<h2 class="mb-1">{{ $s['title'] ?? 'SaaS platforms' }}</h2>
				<p class="mb-0 text-muted">{{ $s['subtitle'] ?? '' }}</p>
			</div>
			<a href="{{ route('saas-platforms') }}" class="btn btn-outline-primary btn-sm mb-0" data-aos="fade-left">View all</a>
		</div>
		<div class="row g-4">
			@foreach($items as $it)
				<div class="col-md-6 col-lg-4" data-aos="fade-up">
					<div class="card h-100 border-0 shadow-sm">
						@if(!empty($it['image_url']))
							<img src="{{ $it['image_url'] }}" class="card-img-top" alt="" loading="lazy" height="180" style="object-fit:cover;max-height:180px" onerror="this.style.display='none'">
						@endif
						<div class="card-body">
							<h5 class="card-title">
								<a href="{{ url($it['url'] ?? '#') }}" class="stretched-link text-decoration-none">{{ $it['title'] ?? '' }}</a>
							</h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($it['summary'] ?? '', 140) }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
