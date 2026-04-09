@extends('layouts.admin')
@section('title', 'Media')
@section('content')
	<x-admin.page-header title="Media library" />

	<x-admin.card class="mb-6">
		<form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
			@csrf
			<div class="min-w-[200px] flex-1">
				<label for="media-file" class="mb-1.5 block text-sm font-medium text-zinc-700">File</label>
				<input
					id="media-file"
					type="file"
					name="file"
					required
					class="block w-full text-sm text-zinc-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100"
				/>
			</div>
			<div class="min-w-[180px] flex-1">
				<label for="media-alt" class="mb-1.5 block text-sm font-medium text-zinc-700">Alt text</label>
				<input
					id="media-alt"
					type="text"
					name="alt_text"
					class="block w-full rounded-[10px] border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
				/>
			</div>
			<div class="shrink-0">
				<x-admin.button variant="primary" type="submit">Upload</x-admin.button>
			</div>
		</form>
	</x-admin.card>

	<div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
		@foreach ($assets as $asset)
			<div data-admin-row>
				<x-admin.card class="group relative h-full">
					<div class="flex flex-col gap-2">
					@if (str_starts_with((string) $asset->mime_type, 'image/'))
						<img
							src="{{ $asset->absoluteUrl() }}"
							alt=""
							class="aspect-video w-full rounded-lg object-cover"
						/>
					@else
						<div
							class="flex aspect-video items-center justify-center rounded-lg bg-zinc-100 text-zinc-400"
						>
							<x-admin.icon name="document-text" class="size-10" />
						</div>
					@endif
					<div class="truncate text-sm font-medium text-zinc-900" title="{{ $asset->filename }}">{{ $asset->filename }}</div>
					<div class="text-xs text-zinc-500">ID: {{ $asset->id }}</div>
					<x-admin.button
						variant="danger"
						type="button"
						class="w-full"
						data-admin-delete="{{ route('admin.media.destroy', $asset) }}"
					>Delete</x-admin.button>
					</div>
				</x-admin.card>
			</div>
		@endforeach
	</div>
	{{ $assets->links('pagination.admin') }}
@endsection
