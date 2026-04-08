@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New case study' : 'Edit case study')
@section('content')
<h1 class="h4 mb-3">Case study</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.case-studies.store') : route('admin.case-studies.update', $caseStudy) }}" id="resource-form" class="card border-0 shadow-sm">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body row g-3">
		<div class="col-md-6"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required value="{{ old('title', $caseStudy->title) }}"></div>
		<div class="col-md-6"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="{{ old('slug', $caseStudy->slug) }}"></div>
		<div class="col-md-6"><label class="form-label">Client</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name', $caseStudy->client_name) }}"></div>
		<div class="col-12"><label class="form-label">Summary</label><input type="text" name="summary" class="form-control" value="{{ old('summary', $caseStudy->summary) }}"></div>
		<div class="col-12"><label class="form-label">Body (HTML)</label><textarea name="body" class="form-control font-monospace small" rows="12">{{ old('body', $caseStudy->body) }}</textarea></div>
		<div class="col-md-4">
			<label class="form-label">Status</label>
			<select name="status" class="form-select">
				<option value="draft" @selected(old('status', $caseStudy->status) === 'draft')>draft</option>
				<option value="published" @selected(old('status', $caseStudy->status) === 'published')>published</option>
			</select>
		</div>
		<div class="col-md-4"><label class="form-label">Published at</label><input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($caseStudy->published_at)->format('Y-m-d\TH:i')) }}"></div>
		<div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="featured" value="1" class="form-check-input" id="feat" @checked(old('featured', $caseStudy->featured))><label class="form-check-label" for="feat">Featured (homepage)</label></div></div>
		<div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('admin.case-studies.index') }}" class="btn btn-link">Cancel</a></div>
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
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); setTimeout(function(){ location='{{ route('admin.case-studies.index') }}'; }, 500); },
		error: function (xhr) { adminToast('Error', 'error'); }
	});
});
</script>
@endpush
