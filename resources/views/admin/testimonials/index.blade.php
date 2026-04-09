@extends('layouts.admin')
@section('title', 'Testimonials')
@section('content')
	<x-admin.page-header title="Testimonials">
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.testimonials.create')">Add testimonial</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Author</th>
					<th class="px-4 py-3 font-semibold">Published</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($testimonials as $t)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3">
							<div class="flex items-center gap-3">
								<span
									class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700"
								>{{ strtoupper(mb_substr($t->author_name, 0, 1)) }}</span>
								<span class="font-medium text-zinc-900">{{ $t->author_name }}</span>
							</div>
						</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$t->is_published ? 'yes' : 'no'" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.testimonials.edit', $t)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.testimonials.destroy', $t) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
@endsection
