@extends('layouts.admin')
@section('title', 'FAQ')
@section('content')
	<x-admin.page-header title="FAQ">
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.faqs.create')">Add FAQ</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Question</th>
					<th class="px-4 py-3 font-semibold">Published</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($faqs as $faq)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="max-w-md px-4 py-3 text-sm text-zinc-800">{{ \Illuminate\Support\Str::limit($faq->question, 80) }}</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$faq->is_published ? 'yes' : 'no'" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.faqs.edit', $faq)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.faqs.destroy', $faq) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
@endsection
