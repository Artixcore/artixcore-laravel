@extends('layouts.admin')
@section('title', 'FAQ')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">FAQ</h1>
	<a href="{{ route('admin.faqs.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Question</th><th>Published</th><th></th></tr></thead>
		<tbody>
			@foreach($faqs as $faq)
				<tr data-admin-row>
					<td>{{ \Illuminate\Support\Str::limit($faq->question, 60) }}</td>
					<td>{{ $faq->is_published ? 'yes' : 'no' }}</td>
					<td class="text-end">
						<a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.faqs.destroy', $faq) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
