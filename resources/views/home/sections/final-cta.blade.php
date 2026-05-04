@php
	$s = $section ?? [];
	$st = $s['settings'] ?? [];
	$title = $s['title'] ?? ($st['cta_title'] ?? 'Ready to build something exceptional?');
	$body = $s['description'] ?? ($st['cta_body'] ?? ($s['subtitle'] ?? ''));
	$btn = $s['button_text'] ?? ($st['cta_button_label'] ?? 'Contact Artixcore');
	$url = $s['button_url'] ?? ($st['cta_button_url'] ?? '/lead');
	$secBtn = $s['secondary_button_text'] ?? null;
	$secUrl = $s['secondary_button_url'] ?? null;
	$teaserTitle = $st['contact_teaser_title'] ?? null;
	$teaserBody = $st['contact_teaser_body'] ?? null;
@endphp
<section class="position-relative z-index-2 py-0 mb-n7">
	<div class="container position-relative">
		<div class="bg-primary rounded position-relative overflow-hidden p-4 p-sm-5">
			<div class="row g-4 align-items-center">
				<div class="col-lg-{{ ($secBtn && $secUrl) ? '7' : '8' }}">
					<h3 class="text-white mb-2">{{ $title }}</h3>
					@if($body !== '')
						<p class="text-white mb-0 opacity-75">{{ $body }}</p>
					@endif
				</div>
				<div class="col-lg-{{ ($secBtn && $secUrl) ? '5' : '4' }} text-lg-end d-flex flex-column flex-lg-row gap-2 justify-content-lg-end align-items-lg-center">
					<a href="{{ url($url) }}" class="btn btn-dark mb-0">{{ $btn }}</a>
					@if($secBtn && $secUrl)
						<a href="{{ url($secUrl) }}" class="btn btn-light mb-0">{{ $secBtn }}</a>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>
@if($teaserTitle || $teaserBody)
<section class="pt-8 pb-0">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-8 text-center py-5">
				@if($teaserTitle)
					<h3>{{ $teaserTitle }}</h3>
				@endif
				@if($teaserBody)
					<p class="text-muted">{{ $teaserBody }}</p>
				@endif
				<a href="{{ route('lead.create') }}" class="btn btn-primary mb-0">Contact us</a>
			</div>
		</div>
	</div>
</section>
@endif
