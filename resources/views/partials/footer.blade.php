@php
	$social = $site->social_links ?? [];
@endphp
<footer class="bg-dark position-relative overflow-hidden pb-0 pt-6 pt-lg-8" data-bs-theme="dark">
	<div class="container position-relative mt-5">
		<div class="row g-4 justify-content-between">
			<div class="col-lg-3">
				<a class="me-0" href="{{ url('/') }}">
					<img class="light-mode-item h-40px" src="{{ $site->logoMedia?->absoluteUrl() ?? asset('logo.svg') }}" alt="{{ $site->site_name ?? 'Artixcore' }}">
					<img class="dark-mode-item h-40px" src="{{ $site->logoMedia?->absoluteUrl() ?? asset('logo.svg') }}" alt="{{ $site->site_name ?? 'Artixcore' }}">
				</a>
				<p class="mt-4 mb-2">{{ $site->default_meta_description ?? 'Artixcore delivers SaaS, AI, mobile, Web3, and automation solutions for ambitious teams.' }}</p>
			</div>
			<div class="col-lg-8 col-xxl-7">
				<div class="row g-4">
					<div class="col-6 col-md-4">
						<h6 class="mb-2 mb-md-4">Explore</h6>
						<ul class="nav flex-column">
							@foreach($footerNavLinks as $link)
								<li class="nav-item"><a class="nav-link pt-0" href="{{ url($link['url']) }}">{{ $link['label'] }}</a></li>
							@endforeach
							<li class="nav-item"><a class="nav-link" href="{{ route('privacy') }}">Privacy</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('terms') }}">Terms</a></li>
						</ul>
					</div>
					<div class="col-6 col-md-4">
						<h6 class="mb-2 mb-md-4">Contact</h6>
						<ul class="nav flex-column">
							@if($site->contact_email)
								<li class="nav-item"><a class="nav-link pt-0" href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a></li>
							@endif
							<li class="nav-item"><a class="nav-link" href="{{ route('lead.create') }}">Contact form</a></li>
						</ul>
					</div>
					<div class="col-md-4">
						<h6 class="mb-2 mb-md-4">Follow</h6>
						<ul class="list-inline mb-0 mt-3">
							@if(!empty($social['facebook']))
								<li class="list-inline-item"><a class="btn btn-xs btn-icon btn-light" href="{{ $social['facebook'] }}" target="_blank" rel="noopener"><i class="fab fa-fw fa-facebook-f lh-base"></i></a></li>
							@endif
							@if(!empty($social['instagram']))
								<li class="list-inline-item"><a class="btn btn-xs btn-icon btn-light" href="{{ $social['instagram'] }}" target="_blank" rel="noopener"><i class="fab fa-fw fa-instagram lh-base"></i></a></li>
							@endif
							@if(!empty($social['twitter']))
								<li class="list-inline-item"><a class="btn btn-xs btn-icon btn-light" href="{{ $social['twitter'] }}" target="_blank" rel="noopener"><i class="fab fa-fw fa-twitter lh-base"></i></a></li>
							@endif
							@if(!empty($social['linkedin']))
								<li class="list-inline-item"><a class="btn btn-xs btn-icon btn-light" href="{{ $social['linkedin'] }}" target="_blank" rel="noopener"><i class="fab fa-fw fa-linkedin-in lh-base"></i></a></li>
							@endif
							@if(!empty($social['youtube']))
								<li class="list-inline-item"><a class="btn btn-xs btn-icon btn-light" href="{{ $social['youtube'] }}" target="_blank" rel="noopener"><i class="fab fa-fw fa-youtube lh-base"></i></a></li>
							@endif
						</ul>
					</div>
				</div>
			</div>
		</div>
		<hr class="mt-4 mb-0">
		<div class="d-md-flex justify-content-between align-items-center text-center text-lg-start py-4">
			<div class="text-body">© {{ date('Y') }} {{ $site->site_name ?? 'Artixcore' }}. All rights reserved.</div>
		</div>
	</div>
</footer>
<div class="back-top"></div>
