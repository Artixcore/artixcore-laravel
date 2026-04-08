@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New legal page' : 'Edit legal page')
@section('content')
<h1 class="h4 mb-3">Legal page</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.legal-pages.store') : route('admin.legal-pages.update', $legalPage) }}" id="resource-form" class="card border-0 shadow-sm">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body row g-3">
		<div class="col-md-6"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required value="{{ old('title', $legalPage->title) }}"></div>
		<div class="col-md-6"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="{{ old('slug', $legalPage->slug) }}" placeholder="privacy-policy"></div>
		<div class="col-12"><label class="form-label">Body (HTML)</label><textarea name="body" class="form-control font-monospace small" rows="16" required>{{ old('body', $legalPage->body) }}</textarea></div>
		<div class="col-md-6"><label class="form-label">Meta title</label><input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $legalPage->meta_title) }}"></div>
		<div class="col-md-6"><label class="form-label">Meta description</label><input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $legalPage->meta_description) }}"></div>
		<div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('admin.legal-pages.index') }}" class="btn btn-link">Cancel</a></div>
	</div>
</form>
@endsection
@push('scripts')
<script>
$('#resource-form').on('submit', function (e) {
	e.preventDefault();
	$.ajax({
		url: $(this).attr('action'),
		type: 'POST',
		data: $(this).serialize(),
		headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); setTimeout(function(){ location='{{ route('admin.legal-pages.index') }}'; }, 500); },
		error: function (xhr) { adminToast('Error', 'error'); }
	});
});
</script>
@endpush
