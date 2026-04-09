@extends('layouts.admin')
@section('title', 'Contact messages')
@section('content')
	<x-admin.page-header title="Contact inbox" />

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Date</th>
					<th class="px-4 py-3 font-semibold">From</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($messages as $m)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600">{{ $m->created_at->format('Y-m-d H:i') }}</td>
						<td class="px-4 py-3">
							<div class="font-medium text-zinc-900">{{ $m->name }}</div>
							<div class="text-sm text-zinc-500">{{ $m->email }}</div>
						</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$m->read_at ? 'read' : 'unread'" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.contact-messages.show', $m)">Open</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.contact-messages.destroy', $m) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
	{{ $messages->links('pagination.admin') }}
@endsection
