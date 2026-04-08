@php
	$services = $headerMegaContext['services'] ?? collect();
	$articles = $headerMegaContext['articles'] ?? collect();
	$count = $services->count();
	$half = $count > 0 ? (int) ceil($count / 2) : 0;
	$col1 = $half > 0 ? $services->take($half) : collect();
	$col2 = $half > 0 ? $services->skip($half) : collect();
	$menuId = 'megaMenuServices'.$idx;
	$navActive = request()->routeIs('services.*');
@endphp
<li class="nav-item dropdown dropdown-fullwidth">
	<a class="nav-link dropdown-toggle {{ $navActive ? 'active' : '' }}" href="#" id="{{ $menuId }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">{{ $navItem['label'] }}</a>
	<div class="dropdown-menu py-0" aria-labelledby="{{ $menuId }}">
		<div class="row p-2 p-sm-4 g-4">
			<div class="col-md-6 col-xl-3">
				<ul class="list-unstyled mb-0">
					<li class="dropdown-header h6 mb-2">Services</li>
					@forelse($col1 as $service)
						<li>
							<a class="dropdown-item" href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a>
						</li>
					@empty
						<li><a class="dropdown-item" href="{{ route('services.index') }}">View all services</a></li>
					@endforelse
				</ul>
			</div>
			<div class="col-md-6 col-xl-3">
				@if($col2->isNotEmpty())
					<ul class="list-unstyled mb-0">
						<li class="dropdown-header h6 mb-2">More</li>
						@foreach($col2 as $service)
							<li>
								<a class="dropdown-item" href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a>
							</li>
						@endforeach
					</ul>
				@endif
			</div>
			<div class="col-md-6 col-xl-3">
				<span class="dropdown-header h6 mb-2">Company</span>
				<div class="dropdown-item d-flex bg-light-hover position-relative text-wrap py-3">
					<div class="icon-md border bg-body rounded flex-shrink-0"><i class="bi bi-boxes heading-color fs-6"></i></div>
					<div class="ms-2">
						<a class="stretched-link heading-color fw-bold mb-0" href="{{ route('about') }}">About</a>
						<p class="mb-0 text-body small">Mission, vision, and how we work with teams.</p>
					</div>
				</div>
				<div class="dropdown-item d-flex bg-light-hover position-relative text-wrap py-3">
					<div class="icon-md border bg-body rounded flex-shrink-0"><i class="bi bi-rocket heading-color fs-6"></i></div>
					<div class="ms-2">
						<a class="stretched-link heading-color fw-bold mb-0" href="{{ route('careers') }}">Careers</a>
						<p class="mb-0 text-body small">Open roles and how to join Artixcore.</p>
					</div>
				</div>
				<div class="dropdown-item d-flex bg-light-hover position-relative text-wrap py-3">
					<div class="icon-md border bg-body rounded flex-shrink-0"><i class="bi bi-kanban heading-color fs-6"></i></div>
					<div class="ms-2">
						<a class="stretched-link heading-color fw-bold mb-0" href="{{ route('portfolio.index') }}">Work</a>
						<p class="mb-0 text-body small">Selected case studies and client outcomes.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<span class="dropdown-header h6 mb-2">Recent articles</span>
				@forelse($articles as $i => $article)
					@php $placeholderImg = asset('theme/images/blog/4by4/0'.(($i % 3) + 1).'.jpg'); @endphp
					<div class="dropdown-item bg-light-hover d-flex align-items-sm-center gap-2 position-relative {{ $loop->last ? '' : 'mb-3' }}">
						<img src="{{ $placeholderImg }}" class="rounded icon-lg object-fit-cover" width="56" height="56" alt="">
						<p class="text-wrap fw-bold mb-0">
							<a href="{{ route('blog.show', $article->slug) }}" class="stretched-link heading-color text-primary-hover">{{ $article->title }}</a>
						</p>
					</div>
				@empty
					<div class="dropdown-item small text-muted mb-0">
						<a href="{{ route('blog.index') }}" class="fw-bold">Browse the blog</a>
					</div>
				@endforelse
			</div>
		</div>
		<hr class="mx-2 mx-sm-4 my-0">
		<div class="p-3 px-sm-4 pb-sm-4">
			<div class="d-sm-flex justify-content-between align-items-center px-1">
				<div class="me-3 mb-2 mb-sm-0">
					<h6 class="mb-2 mb-sm-0">Need a tailored scope?</h6>
					<small class="mb-0">Tell us about your product—we will map the right services and timeline.</small>
				</div>
				<a href="{{ route('contact') }}" class="btn btn-sm btn-primary">Get in touch</a>
			</div>
		</div>
	</div>
</li>
