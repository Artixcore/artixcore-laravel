@extends('layouts.admin')
@section('title', 'Articles')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h4 mb-0">Articles</h1>
	<a href="{{ route('admin.articles.create') }}" class="btn btn-sm btn-primary">Add</a>
</div>
<div class="table-responsive card border-0 shadow-sm">
	<table class="table table-hover mb-0 small">
		<thead><tr><th>Title</th><th>Status</th><th>Updated</th><th></th></tr></thead>
		<tbody>
			@foreach($articles as $article)
				<tr data-admin-row>
					<td>{{ $article->title }}</td>
					<td>{{ $article->status }}</td>
					<td>{{ $article->updated_at->format('Y-m-d') }}</td>
					<td class="text-end">
						<a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
						<button type="button" class="btn btn-sm btn-outline-danger" data-admin-delete="{{ route('admin.articles.destroy', $article) }}">Delete</button>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
{{ $articles->links() }}
@endsection
