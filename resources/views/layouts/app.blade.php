<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>@yield('meta_title', $site->default_meta_title ?? $site->site_name ?? config('app.name'))</title>
	@if($description = trim($__env->yieldContent('meta_description', $site->default_meta_description ?? '')))
		<meta name="description" content="{{ $description }}">
	@endif
	<link rel="shortcut icon" href="{{ $site->faviconMedia?->absoluteUrl() ?? asset('theme/images/favicon.ico') }}">
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
@include('partials.header')

<main>
	@yield('content')
</main>

@include('partials.footer')
@include('partials.scripts')
@stack('scripts')
</body>
</html>
