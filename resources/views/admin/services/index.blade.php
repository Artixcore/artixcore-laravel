@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Services</h1>
	<a href="{{ route('admin.services.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Title</th><th>Slug</th><th>Status</th><th></th></tr></thead>
		<tbody>
			@foreach($services as $service)
				<tr data-admin-row>
					<td>{{ $service->title }}</td>
					<td><code>{{ $service->slug }}</code></td>
					<td>{{ $service->status }}</td>
					<td class="text-end">
						<a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.services.destroy', $service) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
