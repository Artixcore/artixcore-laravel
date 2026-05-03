<aside class="w-full border-b border-zinc-200 bg-white md:w-56 md:border-b-0 md:border-e">
	<div class="flex h-14 items-center gap-2 border-b border-zinc-100 px-4">
		<span class="flex size-8 items-center justify-center rounded-lg bg-red-700 text-xs font-bold text-white">M</span>
		<span class="truncate text-sm font-semibold text-zinc-900">Master</span>
	</div>
	<nav class="flex flex-wrap gap-1 p-2 md:flex-col">
		<a href="{{ route('master.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('master.dashboard') ? 'bg-red-50 text-red-800' : 'text-zinc-700 hover:bg-zinc-50' }}">Overview</a>
		@can('security.manage')
			<a href="{{ route('master.security.access-control') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('master.security.*') ? 'bg-red-50 text-red-800' : 'text-zinc-700 hover:bg-zinc-50' }}">IP allowlist</a>
		@endcan
		<a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Blade admin</a>
	</nav>
</aside>
