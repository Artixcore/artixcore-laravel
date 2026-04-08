@extends('layouts.admin')
@section('title', 'Case studies')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Case studies</h1>
	<a href="{{ route('admin.case-studies.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Title</th><th>Client</th><th>Status</th><th></th></tr></thead>
		<tbody>
			@foreach($caseStudies as $cs)
				<tr data-admin-row>
					<td>{{ $cs->title }}</td>
					<td>{{ $cs->client_name }}</td>
					<td>{{ $cs->status }}</td>
					<td class="text-end">
						<a href="{{ route('admin.case-studies.edit', $cs) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.case-studies.destroy', $cs) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
{{ $caseStudies->links() }}
@endsection
