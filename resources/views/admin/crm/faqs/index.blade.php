@extends('layouts.admin')

@section('title', 'CRM FAQs')

@section('content')
	<x-admin.page-header title="FAQ hub">
		<x-slot:subtitle>Search, attach to content, and publish</x-slot:subtitle>
		<x-slot:actions>
			<x-admin.button href="{{ route('admin.faqs.create') }}">New FAQ (classic)</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	@include('admin.crm._nav')

	<x-admin.card class="mb-4 p-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<div class="min-w-[12rem] flex-1">
				<label class="admin-field-label" for="fq">Search</label>
				<input type="search" name="q" id="fq" class="admin-field-input w-full" value="{{ $filters['q'] ?? '' }}">
			</div>
			<div>
				<label class="admin-field-label" for="fst">Status</label>
				<select name="status" id="fst" class="admin-field-input min-w-[10rem]">
					<option value="">All</option>
					@foreach (['draft', 'published', 'archived'] as $st)
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
					<th class="px-4 py-3">Question</th>
					<th class="px-4 py-3">Status</th>
					<th class="px-4 py-3">Link</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($faqs as $faq)
					<tr>
						<td class="px-4 py-3 text-sm">{{ \Illuminate\Support\Str::limit($faq->question, 80) }}</td>
						<td class="px-4 py-3 text-xs">{{ $faq->status ?? ($faq->is_published ? 'published' : 'draft') }}</td>
						<td class="px-4 py-3 text-sm">
							<details>
								<summary class="cursor-pointer text-indigo-600 hover:underline">Attach</summary>
								<form method="post" action="{{ route('admin.crm.faqs.attach', $faq) }}" class="mt-2 space-y-2 rounded border border-zinc-200 bg-zinc-50 p-2" data-admin-ajax-form>
									@csrf
									<select name="faqable_key" class="admin-field-input w-full text-xs" required>
										@foreach ($faqableTypeLabels as $key)
											<option value="{{ $key }}">{{ $key }}</option>
										@endforeach
									</select>
									<input type="number" name="faqable_id" class="admin-field-input w-full text-xs" placeholder="Entity ID" required min="1">
									<input type="number" name="sort_order" class="admin-field-input w-full text-xs" placeholder="Sort" value="0" min="0">
									<x-admin.button type="submit" class="text-xs">Attach</x-admin.button>
								</form>
								<form method="post" action="{{ route('admin.crm.faqs.detach', $faq) }}" class="mt-2 space-y-2 rounded border border-zinc-200 bg-white p-2" data-admin-ajax-form>
									@csrf
									<select name="faqable_key" class="admin-field-input w-full text-xs" required>
										@foreach ($faqableTypeLabels as $key)
											<option value="{{ $key }}">{{ $key }}</option>
										@endforeach
									</select>
									<input type="number" name="faqable_id" class="admin-field-input w-full text-xs" placeholder="Entity ID" required min="1">
									<x-admin.button type="submit" variant="danger" class="text-xs">Detach</x-admin.button>
								</form>
							</details>
							<a href="{{ route('admin.faqs.edit', $faq) }}" class="ml-2 text-indigo-600 hover:underline">Edit</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $faqs->links() }}</div>
	</x-admin.card>
@endsection
