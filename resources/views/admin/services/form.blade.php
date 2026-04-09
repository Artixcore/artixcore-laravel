@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New service' : 'Edit service')

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New service' : 'Edit service'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.services.store') : route('admin.services.update', $service) }}"
			class="space-y-6"
			id="resource-form"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $service->title) }}" required />
				<x-admin.input name="slug" label="Slug (optional)" value="{{ old('slug', $service->slug) }}" />
			</div>
			<x-admin.input name="summary" label="Summary" value="{{ old('summary', $service->summary) }}" />
			<x-admin.textarea name="body" label="Body (HTML ok)" rows="8" class="font-mono text-xs">{{ old('body', $service->body) }}</x-admin.textarea>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<x-admin.input name="icon" label="Icon (Bootstrap Icons class)" value="{{ old('icon', $service->icon) }}" placeholder="bi bi-stack" />
				<x-admin.input
					name="featured_image_media_id"
					label="Featured image media ID"
					type="number"
					value="{{ old('featured_image_media_id', $service->featured_image_media_id) }}"
				/>
				<x-admin.input
					name="sort_order"
					label="Sort order"
					type="number"
					value="{{ old('sort_order', $service->sort_order ?? 0) }}"
				/>
			</div>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.select name="status" label="Status">
					<option value="draft" @selected(old('status', $service->status) === 'draft')>draft</option>
					<option value="published" @selected(old('status', $service->status) === 'published')>published</option>
				</x-admin.select>
				<div>
					<label for="published_at" class="admin-field-label">Published at</label>
					<input
						id="published_at"
						type="datetime-local"
						name="published_at"
						class="admin-field-input"
						value="{{ old('published_at', optional($service->published_at)->format('Y-m-d\TH:i')) }}"
					/>
				</div>
			</div>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.services.index')">Cancel</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection

@push('scripts')
	<script>
		$('#resource-form').on('submit', function (e) {
			e.preventDefault();
			var $f = $(this);
			$.ajax({
				url: $f.attr('action'),
				type: 'POST',
				data: $f.serialize(),
				headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
				success: function (res) {
					adminToast(res.message || 'Saved.', 'success');
					setTimeout(function () {
						window.location = '{{ route('admin.services.index') }}';
					}, 600);
				},
				error: function (xhr) {
					var m = 'Could not save';
					if (xhr.responseJSON && xhr.responseJSON.errors)
						m = Object.values(xhr.responseJSON.errors).flat().join(' ');
					adminToast(m, 'error');
				},
			});
		});
	</script>
@endpush
