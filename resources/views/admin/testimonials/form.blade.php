@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New testimonial' : 'Edit testimonial')
@section('content')
<h1 class="h4 mb-3">{{ $mode === 'create' ? 'New testimonial' : 'Edit' }}</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.testimonials.store') : route('admin.testimonials.update', $testimonial) }}" id="resource-form" class="card border-0 shadow-sm">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body row g-3">
		<div class="col-md-6"><label class="form-label">Author</label><input type="text" name="author_name" class="form-control" required value="{{ old('author_name', $testimonial->author_name) }}"></div>
		<div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" class="form-control" value="{{ old('role', $testimonial->role) }}"></div>
		<div class="col-md-6"><label class="form-label">Company</label><input type="text" name="company" class="form-control" value="{{ old('company', $testimonial->company) }}"></div>
		<div class="col-md-6"><label class="form-label">Avatar media ID</label><input type="number" name="avatar_media_id" class="form-control" value="{{ old('avatar_media_id', $testimonial->avatar_media_id) }}"></div>
		<div class="col-12"><label class="form-label">Quote</label><textarea name="body" class="form-control" rows="4" required>{{ old('body', $testimonial->body) }}</textarea></div>
		<div class="col-md-4"><label class="form-label">Sort</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}"></div>
		<div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="is_published" value="1" class="form-check-input" id="pub" @checked(old('is_published', $testimonial->is_published ?? true))><label class="form-check-label" for="pub">Published</label></div></div>
		<div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('admin.testimonials.index') }}" class="btn btn-link">Cancel</a></div>
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
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); setTimeout(function(){ location='{{ route('admin.testimonials.index') }}'; }, 500); },
		error: function (xhr) { adminToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error'); }
	});
});
</script>
@endpush
