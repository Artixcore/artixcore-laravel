@extends('layouts.admin')
@section('title', 'Navigation — '.$menu->name)
@section('content')
	<x-admin.page-header :title="$menu->name">
		<x-slot:subtitle>
			Menu key: <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs">{{ $menu->key }}</code>
		</x-slot:subtitle>
		<x-slot:filters>
			<x-admin.button
				variant="secondary"
				:href="route('admin.navigation.index', ['nav_menu' => 'web_primary'])"
				:class="$menu->key === 'web_primary' ? 'ring-2 ring-indigo-500/30' : ''"
			>Header</x-admin.button>
			<x-admin.button
				variant="secondary"
				:href="route('admin.navigation.index', ['nav_menu' => 'footer'])"
				:class="$menu->key === 'footer' ? 'ring-2 ring-indigo-500/30' : ''"
			>Footer</x-admin.button>
		</x-slot:filters>
		<x-slot:actions>
			<x-admin.button
				variant="primary"
				:href="route('admin.navigation.create', ['nav_menu' => $menu->key])"
			>Add item</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Sort</th>
					<th class="px-4 py-3 font-semibold">Label</th>
					<th class="px-4 py-3 font-semibold">URL / page</th>
					<th class="px-4 py-3 font-semibold">Parent</th>
					<th class="px-4 py-3 font-semibold">Mega</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($items as $row)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600">{{ $row->sort_order }}</td>
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $row->label }}</td>
						<td class="max-w-xs px-4 py-3 text-sm text-zinc-600">
							@if ($row->url)
								<code class="break-all rounded bg-zinc-100 px-1.5 py-0.5 text-xs">{{ \Illuminate\Support\Str::limit($row->url, 48) }}</code>
							@elseif($row->page)
								<span class="text-zinc-500">{{ $row->page->path }}</span>
							@else
								—
							@endif
						</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $row->parent?->label ?? '—' }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ is_array($row->feature_payload) && isset($row->feature_payload['mega']) ? $row->feature_payload['mega'] : '—' }}</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link
									:href="route('admin.navigation.edit', ['nav_menu' => $menu->key, 'nav_item' => $row])"
								>Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.navigation.destroy', ['nav_menu' => $menu->key, 'nav_item' => $row]) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
	@if ($items->isEmpty())
		<p class="mt-4 text-sm text-zinc-500">
			No items yet. Run <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs">php artisan db:seed --class=MarketingBladeSeeder</code> for the marketing header, or add items above.
		</p>
	@endif
@endsection
