@extends('layouts.admin')
@section('title', 'Testimonials')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Testimonials</h1>
	<a href="{{ route('admin.testimonials.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Author</th><th>Published</th><th></th></tr></thead>
		<tbody>
			@foreach($testimonials as $t)
				<tr data-admin-row>
					<td>{{ $t->author_name }}</td>
					<td>{{ $t->is_published ? 'yes' : 'no' }}</td>
					<td class="text-end">
						<a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.testimonials.destroy', $t) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
