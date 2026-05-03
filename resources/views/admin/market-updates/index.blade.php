@extends('layouts.admin')
@section('title', 'Market updates')
@section('content')
	<x-admin.page-header title="Market updates">
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.market-updates.create')">Add market update</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card class="mb-4">
		<form method="get" class="grid grid-cols-1 gap-4 md:grid-cols-6 md:items-end">
			<x-admin.select name="status" label="Status">
				<option value="">Any</option>
				@foreach ([
					\App\Models\MarketUpdate::STATUS_DRAFT => 'Draft',
					\App\Models\MarketUpdate::STATUS_PENDING_REVIEW => 'Pending review',
					\App\Models\MarketUpdate::STATUS_SCHEDULED => 'Scheduled',
					\App\Models\MarketUpdate::STATUS_PUBLISHED => 'Published',
					\App\Models\MarketUpdate::STATUS_ARCHIVED => 'Archived',
				] as $val => $label)
					<option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
				@endforeach
			</x-admin.select>
			<x-admin.select name="author_type" label="Author type">
				<option value="">Any</option>
				<option value="{{ \App\Models\MarketUpdate::AUTHOR_TYPE_AI }}" @selected(($filters['author_type'] ?? '') === \App\Models\MarketUpdate::AUTHOR_TYPE_AI)>AI</option>
				<option value="{{ \App\Models\MarketUpdate::AUTHOR_TYPE_HUMAN }}" @selected(($filters['author_type'] ?? '') === \App\Models\MarketUpdate::AUTHOR_TYPE_HUMAN)>Human</option>
			</x-admin.select>
			<x-admin.input name="market_area" label="Market area" value="{{ $filters['market_area'] ?? '' }}" />
			<div>
				<label class="admin-field-label">Category</label>
				<select name="category_term_id" class="admin-field-input mt-1">
					<option value="">Any</option>
					@foreach ($categoryParents as $parent)
						<option value="{{ $parent->id }}" @selected((string) ($filters['category_term_id'] ?? '') === (string) $parent->id)>{{ $parent->name }}</option>
					@endforeach
				</select>
			</div>
			<x-admin.input name="q" label="Search" value="{{ $filters['q'] ?? '' }}" />
			<div class="flex gap-2">
				<x-admin.button variant="primary" type="submit" class="w-full">Filter</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.market-updates.index')" class="w-full">Reset</x-admin.button>
			</div>
		</form>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Title</th>
					<th class="px-4 py-3 font-semibold">Area</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($marketUpdates as $row)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $row->title }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $row->market_area }}</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$row->status" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.market-updates.edit', $row)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.market-updates.destroy', $row) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
	{{ $marketUpdates->links('pagination.admin') }}
@endsection
