@extends('layouts.admin')

@section('title', 'Site settings')

@section('content')
<h1 class="h4 mb-3">Site settings</h1>
<form method="post" action="{{ route('admin.site-settings.update') }}" class="card border-0 shadow-sm" id="site-settings-form" enctype="multipart/form-data">
	@csrf
	@method('PUT')
	<div class="card-body">
		<div class="mb-3">
			<label class="form-label">Site name</label>
			<input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name) }}">
		</div>
		<div class="mb-3">
			<label class="form-label">Default meta title</label>
			<input type="text" name="default_meta_title" class="form-control" value="{{ old('default_meta_title', $settings->default_meta_title) }}">
		</div>
		<div class="mb-3">
			<label class="form-label">Default meta description</label>
			<textarea name="default_meta_description" class="form-control" rows="3">{{ old('default_meta_description', $settings->default_meta_description) }}</textarea>
		</div>
		<div class="mb-3">
			<label class="form-label">Contact email</label>
			<input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings->contact_email) }}">
		</div>

		<hr class="my-4">
		<h2 class="h6 text-muted mb-3">Branding</h2>
		<p class="small text-muted mb-3">Uploads are stored in <code>storage/app/public/media</code> and listed on the <a href="{{ route('admin.media.index') }}">Media</a> page. Ensure <code>php artisan storage:link</code> has been run so files are web-accessible.</p>

		<div class="mb-3">
			<label class="form-label">Logo</label>
			@if($settings->logoMedia)
				<div class="mb-2"><img src="{{ $settings->logoMedia->absoluteUrl() }}" alt="" class="rounded border" style="max-height: 64px;" data-site-preview="logo"></div>
			@else
				<div class="mb-2 d-none" data-site-preview-logo-wrap><img src="" alt="" class="rounded border" style="max-height: 64px;" data-site-preview="logo"></div>
			@endif
			<input type="file" name="logo" class="form-control" accept="image/*">
		</div>
		<div class="mb-3">
			<label class="form-label">Favicon</label>
			@if($settings->faviconMedia)
				<div class="mb-2"><img src="{{ $settings->faviconMedia->absoluteUrl() }}" alt="" class="rounded border" style="max-height: 48px;" data-site-preview="favicon"></div>
			@else
				<div class="mb-2 d-none" data-site-preview-favicon-wrap><img src="" alt="" class="rounded border" style="max-height: 48px;" data-site-preview="favicon"></div>
			@endif
			<input type="file" name="favicon" class="form-control" accept="image/*,.ico">
		</div>
		<div class="mb-3">
			<label class="form-label">Default Open Graph image</label>
			@if($settings->ogDefaultMedia)
				<div class="mb-2"><img src="{{ $settings->ogDefaultMedia->absoluteUrl() }}" alt="" class="rounded border" style="max-height: 120px;" data-site-preview="og_image"></div>
			@else
				<div class="mb-2 d-none" data-site-preview-og-wrap><img src="" alt="" class="rounded border" style="max-height: 120px;" data-site-preview="og_image"></div>
			@endif
			<input type="file" name="og_image" class="form-control" accept="image/*">
		</div>

		<hr class="my-4">
		<div class="mb-3">
			<label class="form-label">Social links (JSON)</label>
			<textarea name="social_links_json" class="form-control font-monospace small" rows="6" placeholder='{"facebook":"","linkedin":"","twitter":"","instagram":"","youtube":""}'>{{ old('social_links_json', json_encode($settings->social_links ?? new stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
		</div>
		<button type="submit" class="btn btn-primary js-ajax-form">Save</button>
	</div>
</form>
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
		if (wrap) wrap.classList.remove('d-none');
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
			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
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
			}
		});
	});
})();
</script>
@endpush
