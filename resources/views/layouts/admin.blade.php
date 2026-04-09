<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	@vite(['resources/css/admin.css', 'resources/js/admin.js'])
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
	<button
		type="button"
		class="shrink-0 rounded-lg p-1 text-current opacity-70 hover:opacity-100"
		onclick="this.closest('#admin-toast').classList.add('hidden')"
		aria-label="Dismiss"
	>&times;</button>
</div>
<div
	id="admin-sidebar-backdrop"
	class="fixed inset-0 z-40 hidden bg-zinc-900/30 backdrop-blur-[1px] md:hidden"
	aria-hidden="true"
></div>
<div class="flex min-h-screen w-full flex-col md:flex-row">
	@include('admin.partials.sidebar')
	<div class="flex min-h-screen min-w-0 flex-1 flex-col">
		@include('admin.partials.topbar')
		<main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
			@if(session('status'))
				<div
					class="mb-4 rounded-[10px] border border-emerald-200/80 bg-emerald-50 px-3 py-2 text-sm text-emerald-900"
					role="status"
				>{{ session('status') }}</div>
			@endif
			@yield('content')
		</main>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script>
(function () {
	var token = document.querySelector('meta[name="csrf-token"]');
	if (token && window.jQuery) {
		jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token.getAttribute('content') } });
	}
})();
</script>
@stack('scripts')
</body>
</html>
