@php
	$nav = [
		['Dashboard', route('admin.dashboard'), 'bi-speedometer2'],
		['Site settings', route('admin.site-settings.edit'), 'bi-gear'],
		['Marketing JSON', route('admin.marketing-content.edit'), 'bi-braces'],
		['Services', route('admin.services.index'), 'bi-grid'],
		['Testimonials', route('admin.testimonials.index'), 'bi-chat-quote'],
		['FAQ', route('admin.faqs.index'), 'bi-question-circle'],
		['Articles', route('admin.articles.index'), 'bi-journal-text'],
		['Case studies', route('admin.case-studies.index'), 'bi-briefcase'],
		['Legal pages', route('admin.legal-pages.index'), 'bi-file-earmark-text'],
		['Job postings', route('admin.job-postings.index'), 'bi-people'],
		['Contact inbox', route('admin.contact-messages.index'), 'bi-inbox'],
		['Media', route('admin.media.index'), 'bi-image'],
	];
@endphp
<nav class="col-md-3 col-lg-2 d-md-block admin-sidebar bg-white py-4 px-3">
	<div class="fw-bold mb-3">{{ $site->site_name ?? 'Artixcore' }}</div>
	<ul class="nav flex-column gap-1 small">
		@foreach($nav as [$label, $url, $icon])
			<li class="nav-item">
				<a class="nav-link rounded {{ request()->url() === $url ? 'active bg-primary text-white' : 'text-dark' }}" href="{{ $url }}">
					<i class="bi {{ $icon }} me-2"></i>{{ $label }}
				</a>
			</li>
		@endforeach
	</ul>
	<hr>
	<a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm w-100 mb-2" target="_blank">View site</a>
	<form method="post" action="{{ route('admin.logout') }}">
		@csrf
		<button type="submit" class="btn btn-outline-danger btn-sm w-100">Sign out</button>
	</form>
	<p class="small text-muted mt-3 mb-0"><a href="{{ url('/filament') }}" class="text-muted">Filament</a></p>
</nav>
