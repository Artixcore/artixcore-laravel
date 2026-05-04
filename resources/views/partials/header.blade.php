@php
	$logoDefault = asset('logo.svg');
	$logoLight = $site->logoMedia?->absoluteUrl() ?? $logoDefault;
	$logoDark = $site->logoMedia?->absoluteUrl() ?? $logoDefault;
@endphp
<header class="header-sticky header-absolute">
	<nav class="navbar navbar-expand-xl">
		<div class="container">
			<a class="navbar-brand me-0" href="{{ route('home') }}">
				<img class="light-mode-item navbar-brand-item" src="{{ $logoLight }}" alt="{{ $site->site_name ?? 'Artixcore' }}">
				<img class="dark-mode-item navbar-brand-item" src="{{ $logoDark }}" alt="{{ $site->site_name ?? 'Artixcore' }}">
			</a>
			<div class="navbar-collapse collapse" id="navbarCollapse">
				<ul class="navbar-nav navbar-nav-scroll dropdown-hover mx-auto">
					@foreach($primaryNavLinks as $idx => $item)
						@php
							$mega = $item['mega'] ?? null;
							$children = $item['children'] ?? [];
						@endphp
						@if($mega === 'services')
							@include('partials.nav._mega-services', ['navItem' => $item, 'idx' => $idx])
						@elseif($mega === 'portfolio')
							@include('partials.nav._mega-portfolio', ['navItem' => $item, 'idx' => $idx])
						@elseif(count($children) > 0)
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">{{ $item['label'] }}</a>
								<ul class="dropdown-menu {{ count($children) >= 6 ? 'dropdown-menu-size-lg p-3' : '' }}">
									@foreach($children as $child)
										<li><a class="dropdown-item" href="{{ url($child['url']) }}">{{ $child['label'] }}</a></li>
									@endforeach
								</ul>
							</li>
						@else
							@php
								$p = trim($item['url'], '/');
								$navActive = $p === '' ? request()->is('/') : (request()->is($p) || request()->is($p.'/*'));
							@endphp
							<li class="nav-item">
								<a class="nav-link {{ $navActive ? 'active' : '' }}" href="{{ url($item['url']) }}">{{ $item['label'] }}</a>
							</li>
						@endif
					@endforeach
				</ul>
			</div>
			<ul class="nav align-items-center dropdown-hover ms-sm-2">
				
				<li class="nav-item d-none d-sm-block">
					<a href="{{ route('lead.create') }}" class="btn btn-sm btn-primary mb-0">Get started</a>
				</li>
				<li class="nav-item">
					<button class="navbar-toggler ms-sm-3 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-animation"><span></span><span></span><span></span></span>
					</button>
				</li>
			</ul>
		</div>
	</nav>
</header>
