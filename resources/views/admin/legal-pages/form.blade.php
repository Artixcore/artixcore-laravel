@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New legal page' : 'Edit legal page')
@section('content')
	<x-admin.page-header title="Legal page" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.legal-pages.store') : route('admin.legal-pages.update', $legalPage) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $legalPage->title) }}" required />
				<x-admin.input
					name="slug"
					label="Slug"
					value="{{ old('slug', $legalPage->slug) }}"
					placeholder="privacy-policy"
				/>
			</div>
			<x-admin.textarea name="body" label="Body (HTML)" rows="16" class="font-mono text-xs" required>{{ old('body', $legalPage->body) }}</x-admin.textarea>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $legalPage->meta_title) }}" />
				<x-admin.input
					name="meta_description"
					label="Meta description"
					value="{{ old('meta_description', $legalPage->meta_description) }}"
				/>
			</div>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.legal-pages.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.legal-pages.index') }}';
					}, 500);
				},
				error: function () {
					adminToast('Error', 'error');
				},
			});
		});
	</script>
@endpush
