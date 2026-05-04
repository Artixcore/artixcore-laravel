@php
	$s = $section ?? [];
	$items = $s['items'] ?? [];
@endphp
@if(count($items) > 0)
<section>
	<div class="container py-5">
		<div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
			<div>
				<h2 class="mb-1">{{ $s['title'] ?? 'Latest articles' }}</h2>
				<p class="mb-0 text-muted">{{ $s['subtitle'] ?? '' }}</p>
			</div>
			<a href="{{ route('articles.index') }}" class="btn btn-outline-primary btn-sm mb-0">All articles</a>
		</div>
		<div class="row g-4">
			@foreach($items as $it)
				<div class="col-md-4" data-aos="fade-up">
					<div class="card h-100 border-0 shadow-sm">
						@if(!empty($it['image_url']))
							<img src="{{ $it['image_url'] }}" class="card-img-top" alt="" loading="lazy" style="max-height:200px;object-fit:cover" onerror="this.style.display='none'">
						@endif
						<div class="card-body">
							<h5 class="card-title">
								<a href="{{ url($it['url'] ?? '#') }}" class="text-decoration-none">{{ $it['title'] ?? '' }}</a>
							</h5>
							<p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit($it['summary'] ?? '', 110) }}</p>
						</div>
						<div class="card-footer bg-transparent border-0 small text-muted">
							@if(!empty($it['published_at']))
								{{ optional($it['published_at'])->format('M j, Y') }}
							@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endif
