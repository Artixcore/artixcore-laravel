@extends('layouts.admin')
@section('title', 'Media')
@section('content')
<h1 class="h4 mb-3">Media library</h1>
<form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm mb-4">
	@csrf
	<div class="card-body row g-2 align-items-end">
		<div class="col-md-6"><label class="form-label">File</label><input type="file" name="file" class="form-control" required></div>
		<div class="col-md-4"><label class="form-label">Alt text</label><input type="text" name="alt_text" class="form-control"></div>
		<div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Upload</button></div>
	</div>
</form>
<div class="row g-3">
	@foreach($assets as $asset)
		<div class="col-6 col-md-4 col-lg-3" data-admin-row>
			<div class="card border-0 shadow-sm h-100">
				<div class="card-body small">
					@if(str_starts_with((string) $asset->mime_type, 'image/'))
						<img src="{{ $asset->absoluteUrl() }}" alt="" class="img-fluid rounded mb-2">
					@else
						<div class="bg-light rounded p-3 text-center mb-2"><i class="bi bi-file-earmark fs-2"></i></div>
					@endif
					<div class="text-truncate" title="{{ $asset->filename }}">{{ $asset->filename }}</div>
					<div class="text-muted">ID: {{ $asset->id }}</div>
					<form method="post" action="{{ route('admin.media.destroy', $asset) }}" class="mt-2" onsubmit="return confirm('Delete file?');">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
					</form>
				</div>
			</div>
		</div>
	@endforeach
</div>
{{ $assets->links() }}
@endsection
