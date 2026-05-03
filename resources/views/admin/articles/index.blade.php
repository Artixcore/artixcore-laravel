@extends('layouts.admin')
@section('title', 'Articles')
@section('content')
	<x-admin.page-header title="Articles">
		<x-slot:actions>
			@can('ai_articles.generate')
				<x-admin.button variant="ghost" :href="route('admin.ai-article-generator.index')">Ali 1.0 generator</x-admin.button>
			@endcan
			<x-admin.button variant="primary" :href="route('admin.articles.create')">Add article</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card class="mb-4">
		<form method="get" action="{{ route('admin.articles.index') }}" class="flex flex-wrap items-end gap-4 p-4">
			<div class="min-w-[140px]">
				<label class="admin-field-label">Status</label>
				<select name="status" class="admin-field-input mt-1">
					<option value="">Any</option>
					@foreach (['draft', 'pending_review', 'scheduled', 'published', 'archived'] as $st)
						<option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
					@endforeach
				</select>
			</div>
			<div class="min-w-[160px]">
				<label class="admin-field-label">Article type</label>
				<input type="text" name="article_type" value="{{ request('article_type') }}" class="admin-field-input mt-1" placeholder="latest_tech…" />
			</div>
			<div class="min-w-[120px]">
				<label class="admin-field-label">Author</label>
				<select name="author_type" class="admin-field-input mt-1">
					<option value="">Any</option>
					<option value="ai" @selected(request('author_type') === 'ai')>AI</option>
					<option value="human" @selected(request('author_type') === 'human')>Human</option>
				</select>
			</div>
			<div class="min-w-[200px] flex-1">
				<label class="admin-field-label">Search</label>
				<input type="search" name="q" value="{{ request('q') }}" class="admin-field-input mt-1" placeholder="Title / body…" />
			</div>
			<x-admin.button variant="primary" type="submit">Filter</x-admin.button>
		</form>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Title</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="px-4 py-3 font-semibold">Type</th>
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
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $article->article_type ?: '—' }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $article->updated_at->format('Y-m-d') }}</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.articles.edit', $article)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link :href="route('admin.articles.preview', $article)" target="_blank">Preview</x-admin.dropdown-link>
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
