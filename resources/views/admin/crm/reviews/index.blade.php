@extends('layouts.admin')

@section('title', 'CRM reviews')

@section('content')
	<x-admin.page-header title="Client reviews">
		<x-slot:subtitle>Moderation and publishing</x-slot:subtitle>
		<x-slot:actions>
			<x-admin.button variant="secondary" href="{{ route('admin.testimonials.create') }}">Classic form</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	@include('admin.crm._nav')

	<x-admin.card class="mb-4 p-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<div>
				<label class="admin-field-label" for="r-st">Moderation</label>
				<select name="status" id="r-st" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach (['pending', 'approved', 'rejected', 'archived'] as $st)
						<option value="{{ $st }}" @selected(($filters['status'] ?? '') === $st)>{{ $st }}</option>
					@endforeach
				</select>
			</div>
			<x-admin.button variant="secondary" type="submit">Filter</x-admin.button>
		</form>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3">Author</th>
					<th class="px-4 py-3">Company</th>
					<th class="px-4 py-3">Status</th>
					<th class="px-4 py-3">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($reviews as $t)
					<tr>
						<td class="px-4 py-3 text-sm font-medium">{{ $t->author_name }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $t->company ?: '—' }}</td>
						<td class="px-4 py-3 text-xs">{{ $t->moderation_status ?? '—' }}</td>
						<td class="px-4 py-3 text-sm space-x-2">
							<a href="{{ route('admin.testimonials.edit', $t) }}" class="text-indigo-600 hover:underline">Edit</a>
							@can('reviews.publish')
								@if (($t->moderation_status ?? '') !== 'approved')
									<form method="post" action="{{ route('admin.crm.reviews.approve', $t) }}" class="inline" data-admin-ajax-form>
										@csrf
										<button type="submit" class="text-emerald-600 hover:underline">Approve</button>
									</form>
								@endif
								@if (($t->moderation_status ?? '') !== 'rejected')
									<form method="post" action="{{ route('admin.crm.reviews.reject', $t) }}" class="inline" data-admin-ajax-form>
										@csrf
										<button type="submit" class="text-red-600 hover:underline">Reject</button>
									</form>
								@endif
							@endcan
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $reviews->links() }}</div>
	</x-admin.card>
@endsection
