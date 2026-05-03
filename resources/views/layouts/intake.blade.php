<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('meta_title', 'Tell us about your needs') — {{ $site->site_name ?? config('app.name') }}</title>
	@if($description = trim($__env->yieldContent('meta_description', '')))
		<meta name="description" content="{{ $description }}">
	@endif
	@php
		$faviconUploaded = $site->faviconMedia?->absoluteUrl();
	@endphp
	@if($faviconUploaded)
		<link rel="icon" href="{{ $faviconUploaded }}">
	@else
		<link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
	@endif
	<link rel="preconnect" href="https://fonts.googleapis.com/">
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,400&display=swap" rel="stylesheet">
	@vite(['resources/css/intake.css', 'resources/js/intake/main.jsx'])
</head>
<body class="min-h-screen bg-gradient-to-b from-stone-50 via-emerald-50/20 to-stone-100 text-stone-900 antialiased">
	<div class="flex min-h-screen flex-col">
		<header class="flex items-center justify-between gap-4 px-4 py-5 sm:px-8">
			<a href="{{ route('home') }}" class="inline-flex items-center gap-2 opacity-90 transition hover:opacity-100">
				@php
					$logoLight = $site->logoMedia?->absoluteUrl() ?? asset('logo.svg');
				@endphp
				<img src="{{ $logoLight }}" alt="{{ $site->site_name ?? 'Home' }}" class="h-8 w-auto" width="120" height="32">
			</a>
			<a href="{{ route('home') }}" class="text-sm font-medium text-stone-600 underline-offset-4 transition hover:text-stone-900 hover:underline">Back to site</a>
		</header>
		<main class="flex-1">
			@yield('content')
		</main>
	</div>
	@if (config('captcha.driver') === 'turnstile' && filled(config('captcha.turnstile.site_key')))
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
	@endif
</body>
</html>
