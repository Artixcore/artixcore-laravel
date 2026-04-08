@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New article' : 'Edit article')
@section('content')
@php $selected = old('term_ids', $article->exists ? $article->terms->pluck('id')->all() : []); @endphp
<h1 class="h4 mb-3">Article</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.articles.store') : route('admin.articles.update', $article) }}" id="resource-form" class="card border-0 shadow-sm">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body row g-3">
		<div class="col-md-6"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required value="{{ old('title', $article->title) }}"></div>
		<div class="col-md-6"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="{{ old('slug', $article->slug) }}"></div>
		<div class="col-12"><label class="form-label">Summary</label><input type="text" name="summary" class="form-control" value="{{ old('summary', $article->summary) }}"></div>
		<div class="col-12"><label class="form-label">Body (HTML)</label><textarea name="body" class="form-control font-monospace small" rows="12">{{ old('body', $article->body) }}</textarea></div>
		<div class="col-md-6"><label class="form-label">Meta title</label><input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $article->meta_title) }}"></div>
		<div class="col-md-6"><label class="form-label">Meta description</label><input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $article->meta_description) }}"></div>
		<div class="col-md-4">
			<label class="form-label">Status</label>
			<select name="status" class="form-select">
				<option value="draft" @selected(old('status', $article->status) === 'draft')>draft</option>
				<option value="published" @selected(old('status', $article->status) === 'published')>published</option>
			</select>
		</div>
		<div class="col-md-4"><label class="form-label">Published at</label><input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"></div>
		<div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="featured" value="1" class="form-check-input" id="feat" @checked(old('featured', $article->featured))><label class="form-check-label" for="feat">Featured</label></div></div>
		@if($categoryTerms->isNotEmpty())
			<div class="col-12">
				<label class="form-label d-block">Categories</label>
				@foreach($categoryTerms as $term)
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="term_ids[]" value="{{ $term->id }}" id="t{{ $term->id }}" @checked(in_array($term->id, $selected, true))>
						<label class="form-check-label" for="t{{ $term->id }}">{{ $term->name }}</label>
					</div>
				@endforeach
			</div>
		@endif
		<div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('admin.articles.index') }}" class="btn btn-link">Cancel</a></div>
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
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); setTimeout(function(){ location='{{ route('admin.articles.index') }}'; }, 500); },
		error: function (xhr) {
			var m = 'Error';
			if (xhr.responseJSON && xhr.responseJSON.errors) m = Object.values(xhr.responseJSON.errors).flat().join(' ');
			adminToast(m, 'error');
		}
	});
});
</script>
@endpush
