@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New article' : 'Edit article')
@section('content')
	@php $selected = old('term_ids', $article->exists ? $article->terms->pluck('id')->all() : []); @endphp
	<x-admin.page-header :title="$mode === 'create' ? 'New article' : 'Edit article'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.articles.store') : route('admin.articles.update', $article) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $article->title) }}" required />
				<x-admin.input name="slug" label="Slug" value="{{ old('slug', $article->slug) }}" />
			</div>
			<x-admin.input name="summary" label="Summary" value="{{ old('summary', $article->summary) }}" />
			<x-admin.textarea name="body" label="Body (HTML)" rows="12" class="font-mono text-xs">{{ old('body', $article->body) }}</x-admin.textarea>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $article->meta_title) }}" />
				<x-admin.input name="meta_description" label="Meta description" value="{{ old('meta_description', $article->meta_description) }}" />
			</div>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
				<x-admin.select name="status" label="Status">
					<option value="draft" @selected(old('status', $article->status) === 'draft')>draft</option>
					<option value="published" @selected(old('status', $article->status) === 'published')>published</option>
				</x-admin.select>
				<div>
					<label for="published_at" class="admin-field-label">Published at</label>
					<input
						id="published_at"
						type="datetime-local"
						name="published_at"
						class="admin-field-input"
						value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"
					/>
				</div>
				<x-admin.toggle-switch name="featured" label="Featured" :checked="(bool) old('featured', $article->featured)" />
			</div>
			@if ($categoryTerms->isNotEmpty())
				<div>
					<span class="admin-field-label">Categories</span>
					<div class="mt-2 flex flex-wrap gap-4">
						@foreach ($categoryTerms as $term)
							<label class="inline-flex cursor-pointer items-center gap-2 text-sm text-zinc-700">
								<input
									class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
									type="checkbox"
									name="term_ids[]"
									value="{{ $term->id }}"
									id="t{{ $term->id }}"
									@checked(in_array($term->id, $selected, true))
								/>
								{{ $term->name }}
							</label>
						@endforeach
					</div>
				</div>
			@endif
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.articles.index')">Cancel</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection
@push('scripts')
	<script>
		$('#resource-form').on('submit', function (e) {
			e.preventDefault();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: $(this).serialize(),
				headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
				success: function (res) {
					adminToast(res.message || 'Saved.', 'success');
					setTimeout(function () {
						location = '{{ route('admin.articles.index') }}';
					}, 500);
				},
				error: function (xhr) {
					var m = 'Error';
					if (xhr.responseJSON && xhr.responseJSON.errors)
						m = Object.values(xhr.responseJSON.errors).flat().join(' ');
					adminToast(m, 'error');
				},
			});
		});
	</script>
@endpush
