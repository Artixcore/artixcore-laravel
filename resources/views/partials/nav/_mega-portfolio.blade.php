@php
	$portfolioItems = $headerMegaContext['portfolioItems'] ?? collect();
	$caseStudies = $headerMegaContext['caseStudies'] ?? collect();
	$children = $navItem['children'] ?? [];
	$menuId = 'megaMenuPortfolio'.$idx;
	$navActive = request()->routeIs('case-studies.*', 'portfolio.*');
@endphp
<li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle {{ $navActive ? 'active' : '' }}" href="#" id="{{ $menuId }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">{{ $navItem['label'] }}</a>
	<div class="dropdown-menu dropdown-menu-center dropdown-menu-size-xl p-3" aria-labelledby="{{ $menuId }}">
		<div class="row g-xl-3">
			<div class="col-xl-8 d-none d-xl-block">
				<div class="d-flex gap-4 align-items-stretch">
					@forelse($portfolioItems as $pf)
						<div class="card bg-transparent flex-grow-1">
							<div class="card-body px-0 text-start pb-0">
								<h6><a href="{{ route('portfolio.show', $pf->slug) }}" class="heading-color text-decoration-none">{{ $pf->title }}</a></h6>
								@if($pf->short_description)
									<p class="mb-2 small">{{ \Illuminate\Support\Str::limit($pf->short_description, 100) }}</p>
								@endif
								<a class="icon-link icon-link-hover stretched-link mb-0" href="{{ route('portfolio.show', $pf->slug) }}">View project<i class="bi bi-arrow-right"></i></a>
							</div>
						</div>
					@empty
						<div class="card bg-transparent flex-grow-1">
							<div class="card-body px-0 text-start pb-0">
								<h6><a href="{{ route('portfolio.index') }}" class="heading-color text-decoration-none">Portfolio</a></h6>
								<p class="mb-2 small">Explore shipped projects and implementations.</p>
								<a class="icon-link icon-link-hover stretched-link mb-0" href="{{ route('portfolio.index') }}">View all<i class="bi bi-arrow-right"></i></a>
							</div>
						</div>
					@endforelse
					@if($portfolioItems->count() >= 2)
						<div class="vr ms-2 flex-shrink-0 align-self-stretch"></div>
					@endif
				</div>
			</div>
			<div class="col-12 d-xl-none">
				<ul class="list-unstyled mb-3">
					@foreach($portfolioItems as $pf)
						<li><a class="dropdown-item" href="{{ route('portfolio.show', $pf->slug) }}">{{ $pf->title }}</a></li>
					@endforeach
					<li><a class="dropdown-item fw-bold" href="{{ route('portfolio.index') }}">All portfolio</a></li>
				</ul>
			</div>
			<div class="col-xl-4">
				<ul class="list-unstyled mb-0">
					<li class="dropdown-header h6">Explore</li>
					<li><a class="dropdown-item" href="{{ route('portfolio.index') }}">Portfolio</a></li>
					<li><a class="dropdown-item" href="{{ route('case-studies.index') }}">Case studies</a></li>
					@foreach($caseStudies->take(3) as $study)
						<li><a class="dropdown-item small" href="{{ route('case-studies.show', $study->slug) }}">{{ \Illuminate\Support\Str::limit($study->title, 42) }}</a></li>
					@endforeach
					@foreach($children as $child)
						<li><a class="dropdown-item" href="{{ url($child['url']) }}">{{ $child['label'] }}</a></li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</li>
