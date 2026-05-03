@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New testimonial' : 'Edit testimonial')
@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New testimonial' : 'Edit testimonial'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.testimonials.store') : route('admin.testimonials.update', $testimonial) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="author_name" label="Author" value="{{ old('author_name', $testimonial->author_name) }}" required />
				<x-admin.input name="role" label="Role" value="{{ old('role', $testimonial->role) }}" />
				<x-admin.input name="company" label="Company" value="{{ old('company', $testimonial->company) }}" />
				<x-admin.input
					name="avatar_media_id"
					label="Avatar media ID"
					type="number"
					value="{{ old('avatar_media_id', $testimonial->avatar_media_id) }}"
				/>
			</div>
			<x-admin.textarea name="body" label="Quote" rows="4" required>{{ old('body', $testimonial->body) }}</x-admin.textarea>
			<x-admin.input
				name="rating"
				label="Rating (1–5, optional)"
				type="number"
				min="1"
				max="5"
				value="{{ old('rating', $testimonial->rating) }}"
			/>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:items-end">
				<x-admin.input name="sort_order" label="Sort" type="number" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" />
				<x-admin.toggle-switch
					name="is_published"
					label="Published"
					:checked="(bool) old('is_published', $testimonial->is_published ?? true)"
				/>
			</div>
			<input type="hidden" name="featured" value="0" />
			<label class="inline-flex cursor-pointer items-center gap-2 text-sm text-zinc-700">
				<input type="checkbox" name="featured" value="1" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(old('featured', $testimonial->featured ?? false)) />
				Featured (homepage / highlights)
			</label>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.testimonials.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.testimonials.index') }}';
					}, 500);
				},
				error: function (xhr) {
					adminToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error');
				},
			});
		});
	</script>
@endpush
