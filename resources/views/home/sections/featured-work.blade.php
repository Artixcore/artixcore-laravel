@php
	$s = $section ?? [];
	$items = $s['items'] ?? [];
@endphp
@if(count($items) > 0)
<section class="bg-light">
	<div class="container py-5">
		<div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
			<div data-aos="fade-right">
				<h2 class="mb-1">{{ $s['title'] ?? 'Featured work' }}</h2>
				<p class="mb-0 text-muted">{{ $s['subtitle'] ?? '' }}</p>
			</div>
			<a href="{{ route('portfolio.index') }}" class="btn btn-dark btn-sm mb-0" data-aos="fade-left">Portfolio</a>
		</div>
		<div class="row g-4">
			@foreach($items as $it)
				<div class="col-md-6 col-lg-4" data-aos="fade-up">
					<div class="card h-100 border-0 shadow-sm overflow-hidden">
						@if(!empty($it['image_url']))
							<img src="{{ $it['image_url'] }}" class="w-100" alt="" loading="lazy" style="max-height:200px;object-fit:cover" onerror="this.style.display='none'">
						@endif
						<div class="card-body">
							<h5 class="card-title">
								<a href="{{ url($it['url'] ?? '#') }}" class="stretched-link text-decoration-none">{{ $it['title'] ?? '' }}</a>
							</h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($it['summary'] ?? '', 120) }}</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
