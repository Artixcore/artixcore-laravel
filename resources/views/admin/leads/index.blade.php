@extends('layouts.admin')

@section('title', 'Leads')

@section('content')
	<x-admin.page-header title="Leads">
		<x-slot:subtitle>Pipeline from AI chat and other sources.</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card class="mb-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<div>
				<label class="admin-field-label" for="f-status">Status</label>
				<select name="status" id="f-status" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach ($statuses as $st)
						<option value="{{ $st }}" @selected($currentStatus === $st)>{{ ucfirst($st) }}</option>
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
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="px-4 py-3 font-semibold">Created</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($leads as $lead)
					<tr class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-mono text-xs">#{{ $lead->id }}</td>
						<td class="px-4 py-3 text-sm">{{ $lead->name ?: '—' }}</td>
						<td class="px-4 py-3 text-sm">{{ $lead->email ?: '—' }}</td>
						<td class="px-4 py-3"><span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium capitalize">{{ $lead->status }}</span></td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $lead->created_at->diffForHumans() }}</td>
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
