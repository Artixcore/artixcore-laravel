@php
	$logoDefault = asset('theme/images/logo.svg');
	$logoLight = $site->logoMedia?->absoluteUrl() ?? $logoDefault;
	$logoDark = $site->logoMedia?->absoluteUrl() ?? asset('theme/images/logo-light.svg');
@endphp
<header class="header-sticky header-absolute">
	<nav class="navbar navbar-expand-xl">
		<div class="container">
			<a class="navbar-brand me-0" href="{{ url('/') }}">
				<img class="light-mode-item navbar-brand-item" src="{{ $logoLight }}" alt="{{ $site->site_name ?? 'Artixcore' }}">
				<img class="dark-mode-item navbar-brand-item" src="{{ $logoDark }}" alt="{{ $site->site_name ?? 'Artixcore' }}">
			</a>
			<div class="navbar-collapse collapse" id="navbarCollapse">
				<ul class="navbar-nav navbar-nav-scroll dropdown-hover mx-auto">
					@foreach($primaryNavLinks as $item)
						@if(!empty($item['children']))
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">{{ $item['label'] }}</a>
								<ul class="dropdown-menu">
									@foreach($item['children'] as $child)
										<li><a class="dropdown-item" href="{{ url($child['url']) }}">{{ $child['label'] }}</a></li>
									@endforeach
								</ul>
							</li>
						@else
							@php
								$p = trim($item['url'], '/');
								$navActive = $p === '' ? request()->is('/') : request()->is($p) || request()->is($p.'/*');
							@endphp
							<li class="nav-item">
								<a class="nav-link {{ $navActive ? 'active' : '' }}" href="{{ url($item['url']) }}">{{ $item['label'] }}</a>
							</li>
						@endif
					@endforeach
				</ul>
			</div>
			<ul class="nav align-items-center dropdown-hover ms-sm-2">
				<li class="nav-item dropdown dropdown-animation">
					<button class="btn btn-link mb-0 px-2 lh-1" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="bi bi-circle-half theme-icon-active fill-mode fa-fw" viewBox="0 0 16 16">
							<path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
						</svg>
					</button>
					<ul class="dropdown-menu min-w-auto dropdown-menu-end" aria-labelledby="bd-theme">
						<li class="mb-1">
							<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">Light</button>
						</li>
						<li class="mb-1">
							<button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">Dark</button>
						</li>
						<li>
							<button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto">Auto</button>
						</li>
					</ul>
				</li>
				<li class="nav-item d-none d-sm-block">
					<a href="{{ route('contact') }}" class="btn btn-sm btn-primary mb-0">Get started</a>
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
