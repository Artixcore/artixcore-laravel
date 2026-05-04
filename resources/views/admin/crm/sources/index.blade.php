@extends('layouts.admin')

@section('title', 'CRM sources')

@section('content')
	<x-admin.page-header title="Lead sources">
		<x-slot:subtitle>Manage attribution and sorting</x-slot:subtitle>
	</x-admin.page-header>

	@include('admin.crm._nav')

	@can('create', App\Models\CrmSource::class)
		<x-admin.card class="mb-6 p-4">
			<h2 class="text-sm font-semibold text-zinc-900">New source</h2>
			<form method="post" action="{{ route('admin.crm.sources.store') }}" class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" data-ajax-form>
				@csrf
				<div>
					<label class="admin-field-label" for="s-name">Name</label>
					<input id="s-name" name="name" class="admin-field-input w-full" required>
					<p class="mt-1 text-xs text-red-600" data-error-for="name"></p>
				</div>
				<div>
					<label class="admin-field-label" for="s-slug">Slug (optional)</label>
					<input id="s-slug" name="slug" class="admin-field-input w-full">
					<p class="mt-1 text-xs text-red-600" data-error-for="slug"></p>
				</div>
				<div>
					<label class="admin-field-label" for="s-order">Sort</label>
					<input id="s-order" name="sort_order" type="number" class="admin-field-input w-full" value="0" min="0">
					<p class="mt-1 text-xs text-red-600" data-error-for="sort_order"></p>
				</div>
				<div class="flex items-end">
					<label class="flex items-center gap-2 text-sm text-zinc-700">
						<input type="hidden" name="is_active" value="0">
						<input type="checkbox" name="is_active" value="1" checked> Active
					</label>
				</div>
				<div class="sm:col-span-2 lg:col-span-4">
					<label class="admin-field-label" for="s-desc">Description</label>
					<textarea id="s-desc" name="description" rows="2" class="admin-field-input w-full"></textarea>
					<p class="mt-1 text-xs text-red-600" data-error-for="description"></p>
				</div>
				<div class="sm:col-span-2 lg:col-span-4">
					<x-admin.button type="submit">Create source</x-admin.button>
				</div>
			</form>
		</x-admin.card>
	@endcan

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3">Name</th>
					<th class="px-4 py-3">Slug</th>
					<th class="px-4 py-3">Active</th>
					<th class="px-4 py-3">Sort</th>
					<th class="px-4 py-3">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($sources as $src)
					<tr data-admin-row>
						<td class="px-4 py-3 text-sm font-medium">{{ $src->name }}</td>
						<td class="px-4 py-3 font-mono text-xs">{{ $src->slug }}</td>
						<td class="px-4 py-3 text-sm">{{ $src->is_active ? 'Yes' : 'No' }}</td>
						<td class="px-4 py-3 text-sm">{{ $src->sort_order }}</td>
						<td class="px-4 py-3 text-sm">
							@can('update', $src)
								<details class="group">
									<summary class="cursor-pointer text-indigo-600">Edit</summary>
									<form method="post" action="{{ route('admin.crm.sources.update', $src) }}" class="mt-2 space-y-2 rounded-lg border border-zinc-200 bg-zinc-50 p-3" data-ajax-form>
										@csrf
										@method('PATCH')
										<input name="name" class="admin-field-input w-full text-sm" value="{{ $src->name }}" required>
										<input name="slug" class="admin-field-input w-full text-sm font-mono" value="{{ $src->slug }}">
										<input name="sort_order" type="number" class="admin-field-input w-full text-sm" value="{{ $src->sort_order }}">
										<label class="flex items-center gap-2 text-xs">
											<input type="hidden" name="is_active" value="0">
											<input type="checkbox" name="is_active" value="1" @checked($src->is_active)> Active
										</label>
										<textarea name="description" rows="2" class="admin-field-input w-full text-sm">{{ $src->description }}</textarea>
										<x-admin.button type="submit" class="text-xs">Save</x-admin.button>
									</form>
								</details>
							@endcan
							@can('delete', $src)
								<button type="button" class="mt-2 text-xs text-red-600 hover:underline" data-admin-delete="{{ route('admin.crm.sources.destroy', $src) }}">Archive</button>
							@endcan
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
@endsection
