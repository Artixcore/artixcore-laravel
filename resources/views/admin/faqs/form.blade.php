@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New FAQ' : 'Edit FAQ')
@section('content')
	<x-admin.page-header title="FAQ" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.faqs.store') : route('admin.faqs.update', $faq) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<x-admin.input name="question" label="Question" value="{{ old('question', $faq->question) }}" required />
			<x-admin.input name="category" label="Category (optional)" value="{{ old('category', $faq->category) }}" />
			<x-admin.textarea name="answer" label="Answer" rows="4" required>{{ old('answer', $faq->answer) }}</x-admin.textarea>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:items-end">
				<div>
					<label class="admin-field-label" for="faq-status">Workflow status</label>
					<select id="faq-status" name="status" class="admin-field-input w-full">
						@foreach (['draft', 'published', 'archived'] as $st)
							<option value="{{ $st }}" @selected(old('status', $faq->status ?? 'published') === $st)>{{ $st }}</option>
						@endforeach
					</select>
				</div>
				<x-admin.input name="sort_order" label="Sort" type="number" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" />
			</div>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
				<x-admin.toggle-switch
					name="is_published"
					label="Published (legacy toggle)"
					:checked="(bool) old('is_published', $faq->is_published ?? true)"
				/>
				<x-admin.toggle-switch
					name="is_featured"
					label="Featured"
					:checked="(bool) old('is_featured', $faq->is_featured ?? false)"
				/>
			</div>
			<x-admin.input name="meta_title" label="Meta title (optional)" value="{{ old('meta_title', $faq->meta_title) }}" />
			<x-admin.textarea name="meta_description" label="Meta description (optional)" rows="2">{{ old('meta_description', $faq->meta_description) }}</x-admin.textarea>
			<div class="space-y-3 rounded-[10px] border border-zinc-100 bg-zinc-50/50 p-4">
				<input type="hidden" name="show_on_general_faq" value="0" />
				<label class="flex cursor-pointer items-start gap-3 text-sm text-zinc-700">
					<input
						type="checkbox"
						name="show_on_general_faq"
						value="1"
						class="mt-0.5 size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
						@checked(old('show_on_general_faq', $faq->show_on_general_faq ?? true))
					/>
					<span>Show on main FAQ page (<code class="rounded bg-white px-1 text-xs">/faq</code>)</span>
				</label>
				<input type="hidden" name="show_on_saas_page" value="0" />
				<label class="flex cursor-pointer items-start gap-3 text-sm text-zinc-700">
					<input
						type="checkbox"
						name="show_on_saas_page"
						value="1"
						class="mt-0.5 size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
						@checked(old('show_on_saas_page', $faq->show_on_saas_page ?? false))
					/>
					<span>Show on SaaS Platforms page</span>
				</label>
			</div>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.faqs.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.faqs.index') }}';
					}, 500);
				},
				error: function () {
					adminToast('Error', 'error');
				},
			});
		});
	</script>
@endpush
