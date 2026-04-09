@extends('layouts.admin')

@section('title', 'Site settings')

@section('content')
	<x-admin.page-header title="Site settings" />

	<x-admin.card>
		<form method="post" action="{{ route('admin.site-settings.update') }}" id="site-settings-form" enctype="multipart/form-data" class="space-y-6">
			@csrf
			@method('PUT')
			<x-admin.input name="site_name" label="Site name" value="{{ old('site_name', $settings->site_name) }}" />
			<x-admin.input name="default_meta_title" label="Default meta title" value="{{ old('default_meta_title', $settings->default_meta_title) }}" />
			<x-admin.textarea name="default_meta_description" label="Default meta description" rows="3">{{ old('default_meta_description', $settings->default_meta_description) }}</x-admin.textarea>
			<x-admin.input
				name="contact_email"
				label="Contact email"
				type="email"
				value="{{ old('contact_email', $settings->contact_email) }}"
			/>

			<div class="border-t border-zinc-100 pt-6">
				<h2 class="text-sm font-semibold text-zinc-900">Branding</h2>
				<p class="mt-1 text-sm text-zinc-500">
					Uploads are stored in <code class="rounded bg-zinc-100 px-1 text-xs">storage/app/public/media</code> and listed on the
					<a href="{{ route('admin.media.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Media</a> page. Ensure
					<code class="rounded bg-zinc-100 px-1 text-xs">php artisan storage:link</code> has been run so files are web-accessible.
				</p>
			</div>

			<div>
				<label class="admin-field-label">Logo</label>
				@if ($settings->logoMedia)
					<div class="mb-2">
						<img src="{{ $settings->logoMedia->absoluteUrl() }}" alt="" class="max-h-16 rounded-lg border border-zinc-200" data-site-preview="logo">
					</div>
				@else
					<div class="mb-2 hidden" data-site-preview-logo-wrap>
						<img src="" alt="" class="max-h-16 rounded-lg border border-zinc-200" data-site-preview="logo">
					</div>
				@endif
				<input type="file" name="logo" class="admin-field-input" accept="image/*">
			</div>
			<div>
				<label class="admin-field-label">Favicon</label>
				@if ($settings->faviconMedia)
					<div class="mb-2">
						<img src="{{ $settings->faviconMedia->absoluteUrl() }}" alt="" class="max-h-12 rounded-lg border border-zinc-200" data-site-preview="favicon">
					</div>
				@else
					<div class="mb-2 hidden" data-site-preview-favicon-wrap>
						<img src="" alt="" class="max-h-12 rounded-lg border border-zinc-200" data-site-preview="favicon">
					</div>
				@endif
				<input type="file" name="favicon" class="admin-field-input" accept="image/*,.ico">
			</div>
			<div>
				<label class="admin-field-label">Default Open Graph image</label>
				@if ($settings->ogDefaultMedia)
					<div class="mb-2">
						<img src="{{ $settings->ogDefaultMedia->absoluteUrl() }}" alt="" class="max-h-[120px] rounded-lg border border-zinc-200" data-site-preview="og_image">
					</div>
				@else
					<div class="mb-2 hidden" data-site-preview-og-wrap>
						<img src="" alt="" class="max-h-[120px] rounded-lg border border-zinc-200" data-site-preview="og_image">
					</div>
				@endif
				<input type="file" name="og_image" class="admin-field-input" accept="image/*">
			</div>

			<x-admin.textarea name="social_links_json" label="Social links (JSON)" rows="6" class="font-mono text-xs" placeholder='{"facebook":"","linkedin":"","twitter":"","instagram":"","youtube":""}'>{{ old('social_links_json', json_encode($settings->social_links ?? new stdClass(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</x-admin.textarea>

			<x-admin.button variant="primary" type="submit" class="js-ajax-form">Save</x-admin.button>
		</form>
	</x-admin.card>
@endsection

@push('scripts')
	<script>
		(function () {
			function showPreview(key, url) {
				if (!url) return;
				var img = document.querySelector('[data-site-preview="' + key + '"]');
				if (!img) return;
				img.src = url;
				var wrap = img.closest('[data-site-preview-logo-wrap], [data-site-preview-favicon-wrap], [data-site-preview-og-wrap]');
				if (wrap) wrap.classList.remove('hidden');
			}
			$('#site-settings-form').on('submit', function (e) {
				e.preventDefault();
				var form = this;
				var fd = new FormData(form);
				$.ajax({
					url: $(form).attr('action'),
					type: 'POST',
					data: fd,
					processData: false,
					contentType: false,
					headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
					success: function (res) {
						adminToast(res.message || 'Saved.', 'success');
						if (res.previews) {
							if (res.previews.logo) showPreview('logo', res.previews.logo);
							if (res.previews.favicon) showPreview('favicon', res.previews.favicon);
							if (res.previews.og_image) showPreview('og_image', res.previews.og_image);
						}
						$(form).find('input[type=file]').val('');
					},
					error: function (xhr) {
						var m = 'Validation error';
						if (xhr.responseJSON) {
							if (xhr.responseJSON.message) m = xhr.responseJSON.message;
							if (xhr.responseJSON.errors) {
								var first = Object.values(xhr.responseJSON.errors)[0];
								if (first && first[0]) m = first[0];
							}
						}
						adminToast(m, 'error');
					},
				});
			});
		})();
	</script>
@endpush
