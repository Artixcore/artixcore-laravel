<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<title>@yield('title', 'Master') — {{ config('app.name') }}</title>
	@vite(['resources/css/admin.css', 'resources/js/admin.js', 'resources/js/master.js'])
	@stack('styles')
</head>
<body class="admin-app">
<div
	id="admin-toast"
	class="admin-toast admin-toast--success hidden"
	role="status"
	aria-live="polite"
>
	<span id="admin-toast-body" class="flex-1"></span>
	<button type="button" class="shrink-0 rounded-lg p-1 text-current opacity-70 hover:opacity-100" onclick="this.closest('#admin-toast').classList.add('hidden')" aria-label="Dismiss">&times;</button>
</div>
<div class="flex min-h-screen w-full flex-col bg-zinc-50 md:flex-row">
	@include('master.partials.sidebar')
	<div class="flex min-h-screen min-w-0 flex-1 flex-col bg-white">
		@include('master.partials.topbar')
		<main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
			@if (session('status'))
				<div class="mb-4 rounded-[10px] border border-emerald-200/80 bg-emerald-50 px-3 py-2 text-sm text-emerald-900" role="status">{{ session('status') }}</div>
			@endif
			@if (session('warning'))
				<div class="mb-4 rounded-[10px] border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900" role="status">{{ session('warning') }}</div>
			@endif
			@yield('content')
		</main>
	</div>
</div>
@stack('scripts')
</body>
</html>
