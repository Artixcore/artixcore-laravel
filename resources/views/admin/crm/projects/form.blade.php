@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New project' : 'Edit project')

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New project' : 'Edit project'" />

	@include('admin.crm._nav')

	<x-admin.card class="max-w-3xl">
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.crm.projects.store') : route('admin.crm.projects.update', $project) }}"
			data-admin-ajax-form
			class="space-y-4"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PATCH')
			@endif

			<div>
				<label class="admin-field-label" for="contact_id">Contact (optional)</label>
				<select id="contact_id" name="contact_id" class="admin-field-input w-full">
					<option value="">—</option>
					@foreach ($contacts as $c)
						<option value="{{ $c->id }}" @selected((int) old('contact_id', $project->contact_id) === $c->id)>{{ $c->name }}</option>
					@endforeach
				</select>
				<p class="mt-1 text-xs text-red-600" data-error-for="contact_id"></p>
			</div>
			<div>
				<label class="admin-field-label" for="title">Title</label>
				<input id="title" name="title" class="admin-field-input w-full" value="{{ old('title', $project->title) }}" required>
				<p class="mt-1 text-xs text-red-600" data-error-for="title"></p>
			</div>
			<div>
				<label class="admin-field-label" for="slug">Slug (optional)</label>
				<input id="slug" name="slug" class="admin-field-input w-full font-mono text-sm" value="{{ old('slug', $project->slug) }}">
				<p class="mt-1 text-xs text-red-600" data-error-for="slug"></p>
			</div>
			<div class="grid gap-4 sm:grid-cols-2">
				<div>
					<label class="admin-field-label" for="status">Status</label>
					<select id="status" name="status" class="admin-field-input w-full">
						@foreach (App\Models\CrmProject::STATUSES as $st)
							<option value="{{ $st }}" @selected(old('status', $project->status) === $st)>{{ $st }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="status"></p>
				</div>
				<div>
					<label class="admin-field-label" for="currency">Currency</label>
					<input id="currency" name="currency" class="admin-field-input w-full" value="{{ old('currency', $project->currency) }}" maxlength="3" required>
					<p class="mt-1 text-xs text-red-600" data-error-for="currency"></p>
				</div>
				<div>
					<label class="admin-field-label" for="budget_amount">Budget</label>
					<input id="budget_amount" name="budget_amount" type="number" step="0.01" class="admin-field-input w-full" value="{{ old('budget_amount', $project->budget_amount) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="budget_amount"></p>
				</div>
				<div>
					<label class="admin-field-label" for="service_type">Service type</label>
					<input id="service_type" name="service_type" class="admin-field-input w-full" value="{{ old('service_type', $project->service_type) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="service_type"></p>
				</div>
				<div>
					<label class="admin-field-label" for="start_date">Start</label>
					<input id="start_date" name="start_date" type="date" class="admin-field-input w-full" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="start_date"></p>
				</div>
				<div>
					<label class="admin-field-label" for="due_date">Due</label>
					<input id="due_date" name="due_date" type="date" class="admin-field-input w-full" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="due_date"></p>
				</div>
				<div>
					<label class="admin-field-label" for="assigned_to">Assigned</label>
					<select id="assigned_to" name="assigned_to" class="admin-field-input w-full">
						<option value="">—</option>
						@foreach ($admins as $u)
							<option value="{{ $u->id }}" @selected((int) old('assigned_to', $project->assigned_to) === $u->id)>{{ $u->name }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="assigned_to"></p>
				</div>
			</div>
			<div>
				<label class="admin-field-label" for="description">Description</label>
				<textarea id="description" name="description" rows="4" class="admin-field-input w-full">{{ old('description', $project->description) }}</textarea>
				<p class="mt-1 text-xs text-red-600" data-error-for="description"></p>
			</div>
			<div>
				<label class="admin-field-label" for="internal_notes">Internal notes</label>
				<textarea id="internal_notes" name="internal_notes" rows="3" class="admin-field-input w-full">{{ old('internal_notes', $project->internal_notes) }}</textarea>
				<p class="mt-1 text-xs text-red-600" data-error-for="internal_notes"></p>
			</div>
			<div class="flex gap-2">
				<x-admin.button type="submit">Save</x-admin.button>
				<x-admin.button variant="secondary" href="{{ route('admin.crm.projects.index') }}">Cancel</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection
