@extends('layouts.admin')

@section('title', 'AI agents')

@section('content')
	<x-admin.page-header title="AI agents">
		<x-slot:subtitle>Configure assistants for visitor chat and automation.</x-slot:subtitle>
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.ai-agents.create')">New agent</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Name</th>
					<th class="px-4 py-3 font-semibold">Slug</th>
					<th class="px-4 py-3 font-semibold">Focus</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($agents as $agent)
					<tr class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $agent->name }}</td>
						<td class="px-4 py-3"><code class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs">{{ $agent->slug }}</code></td>
						<td class="px-4 py-3 text-sm capitalize text-zinc-600">{{ $agent->focus }}</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$agent->status" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.ai-agents.edit', $agent)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.ai-agents.destroy', $agent) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
@endsection
