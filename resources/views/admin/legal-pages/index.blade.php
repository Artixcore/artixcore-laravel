@extends('layouts.admin')
@section('title', 'Legal pages')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Legal pages</h1>
	<a href="{{ route('admin.legal-pages.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Slug</th><th>Title</th><th></th></tr></thead>
		<tbody>
			@foreach($pages as $p)
				<tr data-admin-row>
					<td><code>{{ $p->slug }}</code></td>
					<td>{{ $p->title }}</td>
					<td class="text-end">
						<a href="{{ route('admin.legal-pages.edit', $p) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.legal-pages.destroy', $p) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
