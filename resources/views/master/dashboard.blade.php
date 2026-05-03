@extends('layouts.master')

@section('title', 'Master overview')
@section('topbar_title', 'Master overview')

@section('content')
	<x-admin.page-header title="System overview">
		<x-slot:subtitle>Security, content volume, and recent audit events</x-slot:subtitle>
	</x-admin.page-header>

	@if (! $ipRulesActiveAdmin || ! $ipRulesActiveMaster)
		<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="status">
			<strong>IP allowlist:</strong> No active rules for admin or master yet — all IPs are allowed until you add rules.
			MAC address filtering is not available for public web apps; use IP allowlisting and 2FA.
		</div>
	@endif

	<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
		<x-admin.card><p class="text-sm text-zinc-500">Leads</p><p class="mt-2 text-2xl font-semibold text-zinc-900">{{ $stats['leads_total'] }}</p></x-admin.card>
		<x-admin.card><p class="text-sm text-zinc-500">New leads</p><p class="mt-2 text-2xl font-semibold text-zinc-900">{{ $stats['leads_new'] }}</p></x-admin.card>
		<x-admin.card><p class="text-sm text-zinc-500">Articles published</p><p class="mt-2 text-2xl font-semibold text-zinc-900">{{ $stats['articles_published'] }}</p></x-admin.card>
		<x-admin.card><p class="text-sm text-zinc-500">Users</p><p class="mt-2 text-2xl font-semibold text-zinc-900">{{ $stats['users'] }}</p></x-admin.card>
	</div>

	<div class="mt-10 grid gap-8 lg:grid-cols-2">
		<x-admin.card>
			<h3 class="text-sm font-semibold text-zinc-900">Recent audit events</h3>
			<ul class="mt-4 max-h-80 space-y-2 overflow-y-auto text-sm">
				@forelse ($recentLogs as $log)
					<li class="rounded border border-zinc-100 px-2 py-1 font-mono text-xs text-zinc-700">
						<span class="text-zinc-500">{{ $log->created_at?->format('Y-m-d H:i') }}</span>
						{{ $log->action }}
					</li>
				@empty
					<li class="text-zinc-500">No events yet.</li>
				@endforelse
			</ul>
		</x-admin.card>
		<x-admin.card>
			<h3 class="text-sm font-semibold text-zinc-900">Staff accounts (sample)</h3>
			<ul class="mt-4 space-y-1 text-sm text-zinc-700">
				@foreach ($adminUsers as $u)
					<li class="flex justify-between gap-2 border-b border-zinc-50 py-1">
						<span class="truncate">{{ $u->name }}</span>
						<span class="shrink-0 text-xs text-zinc-500">{{ $u->roles->pluck('name')->join(', ') }}</span>
					</li>
				@endforeach
			</ul>
		</x-admin.card>
	</div>
@endsection
