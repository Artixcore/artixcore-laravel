@extends('layouts.admin')

@section('title', 'CRM')

@section('content')
	<x-admin.page-header title="CRM dashboard">
		<x-slot:subtitle>Contacts, pipeline, and content signals</x-slot:subtitle>
	</x-admin.page-header>

	@include('admin.crm._nav')

	<div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Total contacts</p>
			<p class="mt-1 text-2xl font-semibold text-zinc-900">{{ number_format($total_contacts) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">New</p>
			<p class="mt-1 text-2xl font-semibold text-indigo-600">{{ number_format($new_contacts) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Qualified</p>
			<p class="mt-1 text-2xl font-semibold text-zinc-900">{{ number_format($qualified_contacts) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Clients (approx.)</p>
			<p class="mt-1 text-2xl font-semibold text-emerald-700">{{ number_format($clients) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Running projects</p>
			<p class="mt-1 text-2xl font-semibold text-zinc-900">{{ number_format($running_projects) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Reviews pending</p>
			<p class="mt-1 text-2xl font-semibold text-amber-600">{{ number_format($pending_reviews) }}</p>
		</x-admin.card>
	</div>

	<div class="grid gap-6 lg:grid-cols-2">
		<x-admin.card class="p-4">
			<h2 class="text-sm font-semibold text-zinc-900">Contacts by source</h2>
			<ul class="mt-3 space-y-2 text-sm text-zinc-700">
				@forelse ($contacts_by_source as $sid => $count)
					<li class="flex justify-between gap-2 border-b border-zinc-100 py-1">
						<span>{{ $contacts_by_source_labels[$sid] ?? ('#'.$sid) }}</span>
						<span class="font-mono">{{ $count }}</span>
					</li>
				@empty
					<li class="text-zinc-500">No data</li>
				@endforelse
			</ul>
		</x-admin.card>
		<x-admin.card class="p-4">
			<h2 class="text-sm font-semibold text-zinc-900">Service interest</h2>
			<ul class="mt-3 space-y-2 text-sm text-zinc-700">
				@forelse ($contacts_by_service_interest as $svc => $count)
					<li class="flex justify-between gap-2 border-b border-zinc-100 py-1">
						<span>{{ $svc }}</span>
						<span class="font-mono">{{ $count }}</span>
					</li>
				@empty
					<li class="text-zinc-500">No data</li>
				@endforelse
			</ul>
		</x-admin.card>
	</div>

	<div class="mt-6 grid gap-6 lg:grid-cols-2">
		<x-admin.card class="p-4">
			<h2 class="text-sm font-semibold text-zinc-900">Recent contacts</h2>
			<ul class="mt-3 divide-y divide-zinc-100 text-sm">
				@foreach ($recent_contacts as $c)
					<li class="py-2">
						<a href="{{ route('admin.crm.contacts.show', $c) }}" class="font-medium text-indigo-600 hover:underline">{{ $c->name }}</a>
						<span class="text-zinc-500"> — {{ $c->email ?: 'no email' }}</span>
					</li>
				@endforeach
			</ul>
		</x-admin.card>
		<x-admin.card class="p-4">
			<h2 class="text-sm font-semibold text-zinc-900">Recent notes</h2>
			<ul class="mt-3 divide-y divide-zinc-100 text-sm text-zinc-700">
				@foreach ($recent_notes as $n)
					<li class="py-2">
						<span class="text-xs text-zinc-500">{{ $n->created_at?->diffForHumans() }}</span>
						<p class="mt-0.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($n->body, 120) }}</p>
					</li>
				@endforeach
			</ul>
		</x-admin.card>
	</div>
@endsection
