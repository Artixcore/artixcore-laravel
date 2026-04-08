@extends('layouts.admin')
@section('title', 'Job postings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Job postings</h1>
	<a href="{{ route('admin.job-postings.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Title</th><th>Published</th><th></th></tr></thead>
		<tbody>
			@foreach($jobs as $job)
				<tr data-admin-row>
					<td>{{ $job->title }}</td>
					<td>{{ $job->is_published ? 'yes' : 'no' }}</td>
					<td class="text-end">
						<a href="{{ route('admin.job-postings.edit', $job) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.job-postings.destroy', $job) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
