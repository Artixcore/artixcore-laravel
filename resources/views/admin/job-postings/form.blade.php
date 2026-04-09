@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New job' : 'Edit job')
@section('content')
	<x-admin.page-header title="Job posting" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.job-postings.store') : route('admin.job-postings.update', $job) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $job->title) }}" required />
				<x-admin.input name="location" label="Location" value="{{ old('location', $job->location) }}" />
				<x-admin.input
					name="employment_type"
					label="Employment type"
					value="{{ old('employment_type', $job->employment_type) }}"
					placeholder="Full-time"
				/>
				<x-admin.input name="sort_order" label="Sort" type="number" value="{{ old('sort_order', $job->sort_order ?? 0) }}" />
			</div>
			<x-admin.textarea name="body" label="Body (HTML)" rows="10" class="font-mono text-xs">{{ old('body', $job->body) }}</x-admin.textarea>
			<x-admin.toggle-switch
				name="is_published"
				label="Published"
				:checked="(bool) old('is_published', $job->is_published ?? false)"
			/>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.job-postings.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.job-postings.index') }}';
					}, 500);
				},
				error: function () {
					adminToast('Error', 'error');
				},
			});
		});
	</script>
@endpush
