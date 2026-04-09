@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New nav item' : 'Edit nav item')
@section('content')
	<x-admin.page-header :title="$menu->name.' — '.($mode === 'create' ? 'New item' : 'Edit item')" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.navigation.store', ['nav_menu' => $menu->key]) : route('admin.navigation.update', ['nav_menu' => $menu->key, 'nav_item' => $item]) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="label" label="Label" value="{{ old('label', $item->label) }}" required />
				<x-admin.input name="sort_order" label="Sort order" type="number" value="{{ old('sort_order', $item->sort_order ?? 0) }}" required />
			</div>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<div>
					<label for="url" class="admin-field-label">URL</label>
					<input
						id="url"
						type="text"
						name="url"
						class="admin-field-input"
						placeholder="/about"
						value="{{ old('url', $item->url) }}"
					/>
					<p class="admin-field-hint">Leave empty if using a page below.</p>
				</div>
				<x-admin.select name="page_id" label="Page">
					<option value="">— None —</option>
					@foreach ($pages as $page)
						<option value="{{ $page->id }}" @selected(old('page_id', $item->page_id) == $page->id)>
							{{ $page->path }} — {{ $page->title }}
						</option>
					@endforeach
				</x-admin.select>
			</div>
			<x-admin.select name="parent_id" label="Parent item">
				<option value="">— Top level —</option>
				@foreach ($parentOptions as $opt)
					<option value="{{ $opt->id }}" @selected(old('parent_id', $item->parent_id) == $opt->id)>{{ $opt->label }}</option>
				@endforeach
			</x-admin.select>
			<div>
				<x-admin.textarea
					name="feature_payload_json"
					label="Feature payload (JSON)"
					rows="3"
					class="font-mono text-xs"
					placeholder='{"mega":"services"}'
				>{{ old('feature_payload_json', is_array($item->feature_payload) ? json_encode($item->feature_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>
				<p class="admin-field-hint">
					Optional. Use <code class="rounded bg-zinc-100 px-1 text-xs">{"mega":"services"}</code> or
					<code class="rounded bg-zinc-100 px-1 text-xs">{"mega":"portfolio"}</code> for mega menus.
				</p>
			</div>
			<div>
				<x-admin.textarea
					name="visibility_json"
					label="Visibility (JSON)"
					rows="2"
					class="font-mono text-xs"
					placeholder='{"contexts":["public"]}'
				>{{ old('visibility_json', is_array($item->visibility) ? json_encode($item->visibility, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>
				<p class="admin-field-hint">Leave empty for default public visibility.</p>
			</div>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.navigation.index', ['nav_menu' => $menu->key])">Cancel</x-admin.button>
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
						location = '{{ route('admin.navigation.index', ['nav_menu' => $menu->key]) }}';
					}, 500);
				},
				error: function (xhr) {
					adminToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error');
				},
			});
		});
	</script>
@endpush
