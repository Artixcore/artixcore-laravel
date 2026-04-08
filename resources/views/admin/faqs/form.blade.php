@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New FAQ' : 'Edit FAQ')
@section('content')
<h1 class="h4 mb-3">FAQ</h1>
<form method="post" action="{{ $mode === 'create' ? route('admin.faqs.store') : route('admin.faqs.update', $faq) }}" id="resource-form" class="card border-0 shadow-sm">
	@csrf
	@if($mode === 'edit') @method('PUT') @endif
	<div class="card-body row g-3">
		<div class="col-12"><label class="form-label">Question</label><input type="text" name="question" class="form-control" required value="{{ old('question', $faq->question) }}"></div>
		<div class="col-12"><label class="form-label">Answer</label><textarea name="answer" class="form-control" rows="4" required>{{ old('answer', $faq->answer) }}</textarea></div>
		<div class="col-md-4"><label class="form-label">Sort</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}"></div>
		<div class="col-md-4 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="is_published" value="1" class="form-check-input" id="pub" @checked(old('is_published', $faq->is_published ?? true))><label class="form-check-label" for="pub">Published</label></div></div>
		<div class="col-12">
			<input type="hidden" name="show_on_general_faq" value="0">
			<div class="form-check"><input type="checkbox" name="show_on_general_faq" value="1" class="form-check-input" id="faq-general" @checked(old('show_on_general_faq', $faq->show_on_general_faq ?? true))><label class="form-check-label" for="faq-general">Show on main FAQ page (<code>/faq</code>)</label></div>
			<input type="hidden" name="show_on_saas_page" value="0">
			<div class="form-check"><input type="checkbox" name="show_on_saas_page" value="1" class="form-check-input" id="faq-saas" @checked(old('show_on_saas_page', $faq->show_on_saas_page ?? false))><label class="form-check-label" for="faq-saas">Show on SaaS Platforms page</label></div>
		</div>
		<div class="col-12"><button type="submit" class="btn btn-primary">Save</button> <a href="{{ route('admin.faqs.index') }}" class="btn btn-link">Cancel</a></div>
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
		success: function (res) { adminToast(res.message || 'Saved.', 'success'); setTimeout(function(){ location='{{ route('admin.faqs.index') }}'; }, 500); },
		error: function (xhr) { adminToast('Error', 'error'); }
	});
});
</script>
@endpush
