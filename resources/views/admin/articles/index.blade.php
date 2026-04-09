@extends('layouts.admin')
@section('title', 'Articles')
@section('content')
	<x-admin.page-header title="Articles">
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.articles.create')">Add article</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Title</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="px-4 py-3 font-semibold">Updated</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($articles as $article)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $article->title }}</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$article->status" />
						</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $article->updated_at->format('Y-m-d') }}</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.articles.edit', $article)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.articles.destroy', $article) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
	{{ $articles->links('pagination.admin') }}
@endsection
