@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New case study' : 'Edit case study')
@section('content')
	<x-admin.page-header title="Case study" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.case-studies.store') : route('admin.case-studies.update', $caseStudy) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $caseStudy->title) }}" required />
				<x-admin.input name="slug" label="Slug" value="{{ old('slug', $caseStudy->slug) }}" />
				<x-admin.input name="client_name" label="Client" value="{{ old('client_name', $caseStudy->client_name) }}" />
			</div>
			<x-admin.input name="summary" label="Summary" value="{{ old('summary', $caseStudy->summary) }}" />
			<x-admin.textarea name="body" label="Body (HTML)" rows="12" class="font-mono text-xs">{{ old('body', $caseStudy->body) }}</x-admin.textarea>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
				<x-admin.select name="status" label="Status">
					<option value="draft" @selected(old('status', $caseStudy->status) === 'draft')>draft</option>
					<option value="published" @selected(old('status', $caseStudy->status) === 'published')>published</option>
				</x-admin.select>
				<div>
					<label for="published_at" class="admin-field-label">Published at</label>
					<input
						id="published_at"
						type="datetime-local"
						name="published_at"
						class="admin-field-input"
						value="{{ old('published_at', optional($caseStudy->published_at)->format('Y-m-d\TH:i')) }}"
					/>
				</div>
				<x-admin.toggle-switch
					name="featured"
					label="Featured (homepage)"
					:checked="(bool) old('featured', $caseStudy->featured)"
				/>
			</div>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.case-studies.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.case-studies.index') }}';
					}, 500);
				},
				error: function () {
					adminToast('Error', 'error');
				},
			});
		});
	</script>
@endpush
