@extends('layouts.admin')

@section('title', 'CRM contacts')

@section('content')
	<x-admin.page-header title="Contacts">
		<x-slot:subtitle>CRM pipeline and assignments</x-slot:subtitle>
		<x-slot:actions>
			@can('create', App\Models\CrmContact::class)
				<x-admin.button href="{{ route('admin.crm.contacts.create') }}">New contact</x-admin.button>
			@endcan
		</x-slot:actions>
	</x-admin.page-header>

	@include('admin.crm._nav')

	<x-admin.card class="mb-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<div class="min-w-[10rem] flex-1">
				<label class="admin-field-label" for="cq">Search</label>
				<input type="search" name="q" id="cq" class="admin-field-input w-full" value="{{ $filters['q'] ?? '' }}">
			</div>
			<div>
				<label class="admin-field-label" for="cst">Status</label>
				<select name="status" id="cst" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach (App\Models\CrmContact::STATUSES as $st)
						<option value="{{ $st }}" @selected(($filters['status'] ?? '') === $st)>{{ $st }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="admin-field-label" for="csrc">Source</label>
				<select name="source_id" id="csrc" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach ($sources as $s)
						<option value="{{ $s->id }}" @selected((int) ($filters['source_id'] ?? 0) === $s->id)>{{ $s->name }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="admin-field-label" for="csv">Service</label>
				<select name="service_id" id="csv" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach ($services as $svc)
						<option value="{{ $svc->id }}" @selected((int) ($filters['service_id'] ?? 0) === $svc->id)>{{ $svc->title }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label class="admin-field-label" for="cas">Assigned</label>
				<select name="assigned_to" id="cas" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach ($admins as $u)
						<option value="{{ $u->id }}" @selected((int) ($filters['assigned_to'] ?? 0) === $u->id)>{{ $u->name }}</option>
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
					<th class="px-4 py-3">Name</th>
					<th class="px-4 py-3">Email</th>
					<th class="px-4 py-3">Status</th>
					<th class="px-4 py-3">Priority</th>
					<th class="px-4 py-3">Source</th>
					<th class="w-px px-4 py-3"><span class="sr-only">Open</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($contacts as $c)
					<tr>
						<td class="px-4 py-3 text-sm font-medium text-zinc-900">{{ $c->name }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $c->email ?: '—' }}</td>
						<td class="px-4 py-3 text-xs">{{ $c->status }}</td>
						<td class="px-4 py-3 text-xs">{{ $c->priority }}</td>
						<td class="px-4 py-3 text-xs text-zinc-600">{{ $c->source?->name ?? '—' }}</td>
						<td class="px-4 py-3 text-right">
							<a href="{{ route('admin.crm.contacts.show', $c) }}" class="text-sm font-medium text-indigo-600 hover:underline">View</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $contacts->links() }}</div>
	</x-admin.card>
@endsection
