@extends('layouts.admin')

@section('title', 'Leads')

@section('content')
	<x-admin.page-header title="Leads">
		<x-slot:subtitle>Pipeline from the website, AI chat, and intake.</x-slot:subtitle>
	</x-admin.page-header>

	<div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Total</p>
			<p class="mt-1 text-2xl font-semibold text-zinc-900">{{ number_format($stats['total']) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">New</p>
			<p class="mt-1 text-2xl font-semibold text-indigo-600">{{ number_format($stats['new']) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Contacted</p>
			<p class="mt-1 text-2xl font-semibold text-zinc-900">{{ number_format($stats['contacted']) }}</p>
		</x-admin.card>
		<x-admin.card class="p-4">
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Converted</p>
			<p class="mt-1 text-2xl font-semibold text-emerald-700">{{ number_format($stats['converted']) }}</p>
		</x-admin.card>
	</div>

	<x-admin.card class="mb-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<div class="min-w-[12rem] flex-1">
				<label class="admin-field-label" for="f-q">Search</label>
				<input type="search" name="q" id="f-q" class="admin-field-input w-full" value="{{ $searchQuery }}" placeholder="Name, email, or service">
			</div>
			<div>
				<label class="admin-field-label" for="f-status">Status</label>
				<select name="status" id="f-status" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach ($statuses as $st)
						<option value="{{ $st }}" @selected($currentStatus === $st)>{{ ucfirst($st) }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="admin-field-label" for="f-service">Service type</label>
				<select name="service_type" id="f-service" class="admin-field-input min-w-[12rem]">
					<option value="">All</option>
					@foreach ($serviceTypes as $st)
						<option value="{{ $st }}" @selected($currentServiceType === $st)>{{ $st }}</option>
					@endforeach
				</select>
			</div>
			<x-admin.button variant="secondary" type="submit">Filter</x-admin.button>
		</form>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">ID</th>
					<th class="px-4 py-3 font-semibold">Name</th>
					<th class="px-4 py-3 font-semibold">Email</th>
					<th class="px-4 py-3 font-semibold">Phone</th>
					<th class="px-4 py-3 font-semibold">Service</th>
					<th class="px-4 py-3 font-semibold">Source</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="px-4 py-3 font-semibold">Submitted</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($leads as $lead)
					<tr class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-mono text-xs">#{{ $lead->id }}</td>
						<td class="px-4 py-3 text-sm">{{ $lead->name ?: '—' }}</td>
						<td class="px-4 py-3 text-sm">{{ $lead->email ?: '—' }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $lead->phone ?: '—' }}</td>
						<td class="px-4 py-3 text-xs text-zinc-700">{{ $lead->service_type ?: ($lead->service_interest ?: '—') }}</td>
						<td class="px-4 py-3 text-xs text-zinc-600">{{ $lead->source ?: '—' }}</td>
						<td class="px-4 py-3"><span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium capitalize">{{ $lead->status }}</span></td>
						<td class="px-4 py-3 text-sm text-zinc-600">
							@if ($lead->submitted_at)
								{{ $lead->submitted_at->format('M j, Y g:i a') }}
							@else
								{{ $lead->created_at->diffForHumans() }}
							@endif
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.button variant="ghost" :href="route('admin.leads.show', $lead)">View</x-admin.button>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $leads->links() }}</div>
	</x-admin.card>
@endsection
