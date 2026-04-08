@extends('layouts.admin')

@section('title', 'Marketing content')

@section('content')
<h1 class="h4 mb-3">Homepage, about &amp; services page (JSON)</h1>
<p class="text-muted small">Structured keys merge with defaults from <code>App\Support\MarketingContent</code>. Invalid JSON will be rejected.</p>
<form method="post" action="{{ route('admin.marketing-content.update') }}" id="marketing-form">
	@csrf
	@method('PUT')
	<div class="mb-3">
		<label class="form-label">Homepage content</label>
		<textarea name="homepage_content_json" class="form-control font-monospace small" rows="18" required>{{ old('homepage_content_json', $homepageJson) }}</textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">About page content</label>
		<textarea name="about_content_json" class="form-control font-monospace small" rows="14" required>{{ old('about_content_json', $aboutJson) }}</textarea>
	</div>
	<div class="mb-3">
		<label class="form-label">Services page content</label>
		<textarea name="services_page_content_json" class="form-control font-monospace small" rows="16" required>{{ old('services_page_content_json', $servicesPageJson) }}</textarea>
	</div>
	<button type="submit" class="btn btn-primary">Save</button>
</form>
@endsection

@push('scripts')
<script>
$('#marketing-form').on('submit', function (e) {
	e.preventDefault();
	var $f = $(this);
	$.ajax({
		url: $f.attr('action'),
		type: 'POST',
		data: $f.serialize(),
		headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); },
		error: function (xhr) {
			var m = 'Invalid JSON or server error';
			if (xhr.responseJSON && xhr.responseJSON.message) m = xhr.responseJSON.message;
			adminToast(m, 'error');
		}
	});
});
</script>
@endpush
