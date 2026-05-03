@extends('layouts.admin')

@section('title', 'CRM projects')

@section('content')
	<x-admin.page-header title="Projects">
		<x-slot:subtitle>Running work linked to contacts</x-slot:subtitle>
		<x-slot:actions>
			@can('create', App\Models\CrmProject::class)
				<x-admin.button href="{{ route('admin.crm.projects.create') }}">New project</x-admin.button>
			@endcan
		</x-slot:actions>
	</x-admin.page-header>

	@include('admin.crm._nav')

	<x-admin.card class="mb-4 p-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<div>
				<label class="admin-field-label" for="p-st">Status</label>
				<select name="status" id="p-st" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach (App\Models\CrmProject::STATUSES as $st)
						<option value="{{ $st }}" @selected(($filters['status'] ?? '') === $st)>{{ $st }}</option>
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
					<th class="px-4 py-3">Title</th>
					<th class="px-4 py-3">Contact</th>
					<th class="px-4 py-3">Status</th>
					<th class="px-4 py-3"></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($projects as $p)
					<tr>
						<td class="px-4 py-3 text-sm font-medium">{{ $p->title }}</td>
						<td class="px-4 py-3 text-sm">
							@if ($p->contact)
								<a href="{{ route('admin.crm.contacts.show', $p->contact) }}" class="text-indigo-600 hover:underline">{{ $p->contact->name }}</a>
							@else
								—
							@endif
						</td>
						<td class="px-4 py-3 text-xs">{{ $p->status }}</td>
						<td class="px-4 py-3 text-right">
							@can('update', $p)
								<a href="{{ route('admin.crm.projects.edit', $p) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>
							@endcan
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $projects->links() }}</div>
	</x-admin.card>
@endsection
