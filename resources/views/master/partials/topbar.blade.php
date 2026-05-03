@php
    $user = auth()->user();
@endphp
<header class="flex h-14 shrink-0 items-center justify-between border-b border-zinc-200 bg-white/90 px-4">
	<h1 class="truncate text-sm font-semibold text-zinc-900">@yield('topbar_title', 'Master admin')</h1>
	<form method="post" action="{{ route('master.logout') }}">
		@csrf
		<button type="submit" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50">Sign out</button>
	</form>
</header>
