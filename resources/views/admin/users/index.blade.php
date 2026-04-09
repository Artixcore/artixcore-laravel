@extends('layouts.admin')

@section('title', 'Users')

@section('content')
	<x-admin.page-header title="Users">
		<x-slot:subtitle>Team accounts with role-based access.</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Name</th>
					<th class="px-4 py-3 font-semibold">Email</th>
					<th class="px-4 py-3 font-semibold">Roles</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($users as $u)
					<tr>
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $u->name }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $u->email }}</td>
						<td class="px-4 py-3 text-sm">{{ $u->roles->pluck('name')->join(', ') ?: '—' }}</td>
						<td class="px-4 py-3 text-right">
							<x-admin.button variant="ghost" :href="route('admin.users.edit', $u)">Roles</x-admin.button>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $users->links() }}</div>
	</x-admin.card>
@endsection
