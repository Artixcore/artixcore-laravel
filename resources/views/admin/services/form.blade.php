@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New service' : 'Edit service')

@section('content')
<h1 class="h4 mb-3">{{ $mode === 'create' ? 'New service' : 'Edit service' }}</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.services.store') : route('admin.services.update', $service) }}" class="card border-0 shadow-sm" id="resource-form">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body">
		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label">Title</label>
				<input type="text" name="title" class="form-control" required value="{{ old('title', $service->title) }}">
			</div>
			<div class="col-md-6">
				<label class="form-label">Slug (optional)</label>
				<input type="text" name="slug" class="form-control" value="{{ old('slug', $service->slug) }}">
			</div>
			<div class="col-12">
				<label class="form-label">Summary</label>
				<input type="text" name="summary" class="form-control" value="{{ old('summary', $service->summary) }}">
			</div>
			<div class="col-12">
				<label class="form-label">Body (HTML ok)</label>
				<textarea name="body" class="form-control font-monospace small" rows="8">{{ old('body', $service->body) }}</textarea>
			</div>
			<div class="col-md-4">
				<label class="form-label">Icon (Bootstrap Icons class)</label>
				<input type="text" name="icon" class="form-control" placeholder="bi bi-stack" value="{{ old('icon', $service->icon) }}">
			</div>
			<div class="col-md-4">
				<label class="form-label">Featured image media ID</label>
				<input type="number" name="featured_image_media_id" class="form-control" value="{{ old('featured_image_media_id', $service->featured_image_media_id) }}">
			</div>
			<div class="col-md-4">
				<label class="form-label">Sort order</label>
				<input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
			</div>
			<div class="col-md-4">
				<label class="form-label">Status</label>
				<select name="status" class="form-select">
					<option value="draft" @selected(old('status', $service->status) === 'draft')>draft</option>
					<option value="published" @selected(old('status', $service->status) === 'published')>published</option>
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label">Published at</label>
				<input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($service->published_at)->format('Y-m-d\TH:i')) }}">
			</div>
		</div>
		<button type="submit" class="btn btn-primary mt-3">Save</button>
		<a href="{{ route('admin.services.index') }}" class="btn btn-link">Cancel</a>
	</div>
</form>
@endsection

@push('scripts')
<script>
$('#resource-form').on('submit', function (e) {
	e.preventDefault();
	var $f = $(this);
	$.ajax({
		url: $f.attr('action'),
		type: 'POST',
		data: $f.serialize(),
		headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
		success: function (res) {
			adminToast(res.message || 'Saved.', 'success');
			setTimeout(function () { window.location = '{{ route('admin.services.index') }}'; }, 600);
		},
		error: function (xhr) {
			var m = 'Could not save';
			if (xhr.responseJSON && xhr.responseJSON.errors) m = Object.values(xhr.responseJSON.errors).flat().join(' ');
			adminToast(m, 'error');
		}
	});
});
</script>
@endpush
