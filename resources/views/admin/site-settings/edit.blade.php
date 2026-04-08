@extends('layouts.admin')

@section('title', 'Site settings')

@section('content')
<h1 class="h4 mb-3">Site settings</h1>
<form method="post" action="{{ route('admin.site-settings.update') }}" class="card border-0 shadow-sm" id="site-settings-form">
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
$('#site-settings-form').on('submit', function (e) {
	e.preventDefault();
	var $f = $(this);
	$.ajax({
		url: $f.attr('action'),
		type: 'POST',
		data: $f.serialize(),
		headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); },
		error: function (xhr) {
			var m = 'Validation error';
			if (xhr.responseJSON && xhr.responseJSON.message) m = xhr.responseJSON.message;
			adminToast(m, 'error');
		}
	});
});
</script>
@endpush
