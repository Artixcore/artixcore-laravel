@extends('layouts.admin')

@section('title', 'Portfolio')

@section('content')
	<x-admin.page-header title="Portfolio">
		<x-slot:actions>
			@can('portfolio_items.create')
				<x-admin.button variant="primary" :href="route('admin.portfolio-items.create')">Add item</x-admin.button>
			@endcan
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Title</th>
					<th class="px-4 py-3 font-semibold">Slug</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($items as $item)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $item->title }}</td>
						<td class="px-4 py-3">
							<code class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs text-zinc-700">{{ $item->slug }}</code>
						</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$item->status" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('portfolio.show', $item->slug)" target="_blank">View public</x-admin.dropdown-link>
								<x-admin.dropdown-link :href="route('admin.portfolio-items.edit', $item)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.portfolio-items.destroy', $item) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		@if ($items->hasPages())
			<div class="border-t border-zinc-100 px-4 py-3">{{ $items->links() }}</div>
		@endif
	</x-admin.card>
@endsection
