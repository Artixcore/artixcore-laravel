@extends('layouts.admin')
@section('title', 'Contact messages')
@section('content')
<h1 class="h4 mb-3">Inbox</h1>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Date</th><th>From</th><th>Read</th><th></th></tr></thead>
		<tbody>
			@foreach($messages as $m)
				<tr data-admin-row>
					<td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
					<td>{{ $m->name }} &lt;{{ $m->email }}&gt;</td>
					<td>{{ $m->read_at ? 'yes' : 'no' }}</td>
					<td class="text-end">
						<a href="{{ route('admin.contact-messages.show', $m) }}" class="btn btn-sm btn-outline-secondary">Open</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.contact-messages.destroy', $m) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
{{ $messages->links() }}
@endsection
