@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New job' : 'Edit job')
@section('content')
<h1 class="h4 mb-3">Job posting</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.job-postings.store') : route('admin.job-postings.update', $job) }}" id="resource-form" class="card border-0 shadow-sm">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body row g-3">
		<div class="col-md-6"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required value="{{ old('title', $job->title) }}"></div>
		<div class="col-md-6"><label class="form-label">Location</label><input type="text" name="location" class="form-control" value="{{ old('location', $job->location) }}"></div>
		<div class="col-md-6"><label class="form-label">Employment type</label><input type="text" name="employment_type" class="form-control" placeholder="Full-time" value="{{ old('employment_type', $job->employment_type) }}"></div>
		<div class="col-md-6"><label class="form-label">Sort</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $job->sort_order ?? 0) }}"></div>
		<div class="col-12"><label class="form-label">Body (HTML)</label><textarea name="body" class="form-control font-monospace small" rows="10">{{ old('body', $job->body) }}</textarea></div>
		<div class="col-12"><div class="form-check"><input type="checkbox" name="is_published" value="1" class="form-check-input" id="pub" @checked(old('is_published', $job->is_published ?? false))><label class="form-check-label" for="pub">Published</label></div></div>
		<div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('admin.job-postings.index') }}" class="btn btn-link">Cancel</a></div>
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
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); setTimeout(function(){ location='{{ route('admin.job-postings.index') }}'; }, 500); },
		error: function () { adminToast('Error', 'error'); }
	});
});
</script>
@endpush
