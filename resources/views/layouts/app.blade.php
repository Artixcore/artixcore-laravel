<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('meta_title', $site->default_meta_title ?? $site->site_name ?? config('app.name'))</title>
	@if($description = trim($__env->yieldContent('meta_description', $site->default_meta_description ?? '')))
		<meta name="description" content="{{ $description }}">
	@endif
	@php
		$robots = trim($__env->yieldContent('meta_robots', 'index, follow'));
	@endphp
	@if($robots !== '')
		<meta name="robots" content="{{ $robots }}">
	@endif
	@php
		$keywords = $__env->hasSection('meta_keywords')
			? trim($__env->yieldContent('meta_keywords'))
			: trim((string) config('marketing.default_keywords', ''));
	@endphp
	@if($keywords !== '')
		<meta name="keywords" content="{{ $keywords }}">
	@endif
	@include('partials.seo-head')
	@include('partials.seo-jsonld')
	@include('partials.seo-gtm-head')
	@php
		$faviconUploaded = $site->faviconMedia?->absoluteUrl();
	@endphp
	@if($faviconUploaded)
		<link rel="icon" href="{{ $faviconUploaded }}">
	@else
		<link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
	@endif
	<link rel="shortcut icon" href="{{ $faviconUploaded ?? asset('theme/images/favicon.ico') }}">
	<link rel="preconnect" href="https://fonts.googleapis.com/">
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('theme/vendor/font-awesome/css/all.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap-icons/bootstrap-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('theme/vendor/swiper/swiper-bundle.min.css') }}">
	@stack('vendor_styles')
	<link rel="stylesheet" href="{{ asset('theme/css/style.css') }}">
	@stack('styles')
</head>
<body>
@include('partials.seo-gtm-body')
@include('partials.header')

<main>
	@yield('content')
</main>

@include('partials.footer')
@if (config('captcha.driver') === 'turnstile')
	@php
		$__turnstileSiteKey = (string) (config('services.turnstile.site_key') ?: config('captcha.turnstile.site_key', ''));
	@endphp
	@if ($__turnstileSiteKey !== '')
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
	@endif
@endif
@include('partials.scripts')
@include('partials.seo-scripts')
@include('partials.ai-chat-widget')
@stack('scripts')
</body>
</html>
