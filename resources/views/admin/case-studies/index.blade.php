@extends('layouts.admin')
@section('title', 'Case studies')
@section('content')
	<x-admin.page-header title="Case studies">
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.case-studies.create')">Add case study</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card class="mb-4">
		<form method="get" class="grid grid-cols-1 gap-4 lg:grid-cols-7 lg:items-end">
			<x-admin.select name="status" label="Status">
				<option value="">Any</option>
				@foreach ([
					\App\Models\CaseStudy::STATUS_DRAFT => 'Draft',
					\App\Models\CaseStudy::STATUS_PENDING_REVIEW => 'Pending review',
					\App\Models\CaseStudy::STATUS_SCHEDULED => 'Scheduled',
					\App\Models\CaseStudy::STATUS_PUBLISHED => 'Published',
					\App\Models\CaseStudy::STATUS_ARCHIVED => 'Archived',
				] as $val => $label)
					<option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
				@endforeach
			</x-admin.select>
			<x-admin.select name="case_study_type" label="Type">
				<option value="">Any</option>
				<option value="{{ \App\Models\CaseStudy::TYPE_REAL }}" @selected(($filters['case_study_type'] ?? '') === \App\Models\CaseStudy::TYPE_REAL)>Real</option>
				<option value="{{ \App\Models\CaseStudy::TYPE_ANONYMIZED }}" @selected(($filters['case_study_type'] ?? '') === \App\Models\CaseStudy::TYPE_ANONYMIZED)>Anonymized</option>
				<option value="{{ \App\Models\CaseStudy::TYPE_CONCEPT }}" @selected(($filters['case_study_type'] ?? '') === \App\Models\CaseStudy::TYPE_CONCEPT)>Concept</option>
			</x-admin.select>
			<x-admin.select name="author_type" label="Author type">
				<option value="">Any</option>
				<option value="{{ \App\Models\CaseStudy::AUTHOR_TYPE_AI }}" @selected(($filters['author_type'] ?? '') === \App\Models\CaseStudy::AUTHOR_TYPE_AI)>AI</option>
				<option value="{{ \App\Models\CaseStudy::AUTHOR_TYPE_HUMAN }}" @selected(($filters['author_type'] ?? '') === \App\Models\CaseStudy::AUTHOR_TYPE_HUMAN)>Human</option>
			</x-admin.select>
			<x-admin.input name="industry" label="Industry" value="{{ $filters['industry'] ?? '' }}" />
			<div>
				<label class="admin-field-label">Category</label>
				<select name="category_term_id" class="admin-field-input mt-1">
					<option value="">Any</option>
					@foreach ($categoryParents as $parent)
						<option value="{{ $parent->id }}" @selected((string) ($filters['category_term_id'] ?? '') === (string) $parent->id)>{{ $parent->name }}</option>
					@endforeach
				</select>
			</div>
			<x-admin.input name="q" label="Search" value="{{ $filters['q'] ?? '' }}" />
			<div class="flex gap-2">
				<x-admin.button variant="primary" type="submit" class="w-full">Filter</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.case-studies.index')" class="w-full">Reset</x-admin.button>
			</div>
		</form>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Title</th>
					<th class="px-4 py-3 font-semibold">Type</th>
					<th class="px-4 py-3 font-semibold">Industry</th>
					<th class="px-4 py-3 font-semibold">Status</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($caseStudies as $cs)
					<tr data-admin-row class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $cs->title }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $cs->case_study_type }}</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $cs->industry }}</td>
						<td class="px-4 py-3">
							<x-admin.status-badge :value="$cs->status" />
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.case-studies.edit', $cs)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.case-studies.destroy', $cs) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
	</x-admin.card>
	{{ $caseStudies->links('pagination.admin') }}
@endsection
