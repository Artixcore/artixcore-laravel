@php
	$ctaTitle = $ctaTitle ?? 'Ready to build?';
	$ctaBody = $ctaBody ?? 'Tell us about your product — we design and ship premium Laravel and React platforms.';
	$ctaLabel = $ctaLabel ?? 'Start a project';
@endphp
<section class="py-6 border-top">
	<div class="container text-center">
		<h2 class="h5 mb-3">{{ $ctaTitle }}</h2>
		<p class="text-muted mb-4">{{ $ctaBody }}</p>
		<a href="{{ route('lead.create') }}" class="btn btn-primary btn-lg">{{ $ctaLabel }}</a>
	</div>
</section>
