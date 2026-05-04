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
			<div class="d-flex align-items-center gap-2 order-xl-2 ms-auto ms-xl-0 flex-shrink-0">
				<div class="d-none d-xl-flex align-items-center gap-2">
					@auth
						@if(auth()->user()->hasRole('master_admin'))
							<a href="{{ route('master.dashboard') }}" class="btn btn-sm btn-outline-secondary mb-0">Master</a>
						@elseif(auth()->user()->can('admin.access'))
							<a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary mb-0">Admin</a>
						@elseif(auth()->user()->can('portal.access'))
							<a href="{{ route('portal') }}" class="btn btn-sm btn-outline-secondary mb-0">My portal</a>
						@endif
					@else
						<a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary mb-0">Login</a>
						<a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary mb-0">Join</a>
					@endauth
					<a href="{{ route('lead.create') }}" class="btn btn-sm btn-primary mb-0">Get started</a>
				</div>
				<button class="navbar-toggler p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-animation"><span></span><span></span><span></span></span>
				</button>
			</div>
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
					<li class="nav-item d-xl-none mt-3 pt-3 border-top">
						<div class="d-grid gap-2 px-2 pb-2">
							@auth
								@if(auth()->user()->hasRole('master_admin'))
									<a href="{{ route('master.dashboard') }}" class="btn btn-sm btn-outline-secondary">Master</a>
								@elseif(auth()->user()->can('admin.access'))
									<a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">Admin</a>
								@elseif(auth()->user()->can('portal.access'))
									<a href="{{ route('portal') }}" class="btn btn-sm btn-outline-secondary">My portal</a>
								@endif
							@else
								<a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Login</a>
								<a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">Join</a>
							@endauth
							<a href="{{ route('lead.create') }}" class="btn btn-sm btn-primary">Get started</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</nav>
</header>
