@php
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
					@forelse($caseStudies as $study)
						<div class="card bg-transparent flex-grow-1">
							<div class="card-body px-0 text-start pb-0">
								<h6><a href="{{ route('case-studies.show', $study->slug) }}" class="heading-color text-decoration-none">{{ $study->title }}</a></h6>
								@if($study->summary)
									<p class="mb-2 small">{{ \Illuminate\Support\Str::limit($study->summary, 100) }}</p>
								@endif
								<a class="icon-link icon-link-hover stretched-link mb-0" href="{{ route('case-studies.show', $study->slug) }}">Learn more<i class="bi bi-arrow-right"></i></a>
							</div>
						</div>
					@empty
						<div class="card bg-transparent flex-grow-1">
							<div class="card-body px-0 text-start pb-0">
								<h6><a href="{{ route('case-studies.index') }}" class="heading-color text-decoration-none">Explore case studies</a></h6>
								<p class="mb-2 small">See case studies and work we have shipped for clients.</p>
								<a class="icon-link icon-link-hover stretched-link mb-0" href="{{ route('case-studies.index') }}">View all<i class="bi bi-arrow-right"></i></a>
							</div>
						</div>
					@endforelse
					@if($caseStudies->count() >= 2)
						<div class="vr ms-2 flex-shrink-0 align-self-stretch"></div>
					@endif
				</div>
			</div>
			<div class="col-12 d-xl-none">
				<ul class="list-unstyled mb-3">
					@foreach($caseStudies as $study)
						<li><a class="dropdown-item" href="{{ route('case-studies.show', $study->slug) }}">{{ $study->title }}</a></li>
					@endforeach
					<li><a class="dropdown-item fw-bold" href="{{ route('case-studies.index') }}">View all case studies</a></li>
				</ul>
			</div>
			<div class="col-xl-4">
				<ul class="list-unstyled mb-0">
					<li class="dropdown-header h6">Case studies</li>
					<li><a class="dropdown-item" href="{{ route('case-studies.index') }}">All projects</a></li>
					@foreach($children as $child)
						<li><a class="dropdown-item" href="{{ url($child['url']) }}">{{ $child['label'] }}</a></li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</li>
