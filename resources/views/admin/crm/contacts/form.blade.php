@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New CRM contact' : 'Edit contact')

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New contact' : 'Edit contact'" />

	@include('admin.crm._nav')

	<x-admin.card class="max-w-4xl">
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.crm.contacts.store') : route('admin.crm.contacts.update', $contact) }}"
			class="space-y-4"
			data-ajax-form
		>
			@csrf
			@if ($mode === 'edit')
				@method('PATCH')
			@endif

			<div class="grid gap-4 sm:grid-cols-2">
				<div>
					<label class="admin-field-label" for="name">Name</label>
					<input id="name" name="name" class="admin-field-input w-full" value="{{ old('name', $contact->name) }}" required>
					<p class="mt-1 text-xs text-red-600" data-error-for="name"></p>
				</div>
				<div>
					<label class="admin-field-label" for="email">Email</label>
					<input id="email" name="email" type="email" class="admin-field-input w-full" value="{{ old('email', $contact->email) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="email"></p>
				</div>
				<div>
					<label class="admin-field-label" for="phone">Phone</label>
					<input id="phone" name="phone" class="admin-field-input w-full" value="{{ old('phone', $contact->phone) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="phone"></p>
				</div>
				<div>
					<label class="admin-field-label" for="company_name">Company</label>
					<input id="company_name" name="company_name" class="admin-field-input w-full" value="{{ old('company_name', $contact->company_name) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="company_name"></p>
				</div>
				<div>
					<label class="admin-field-label" for="job_title">Job title</label>
					<input id="job_title" name="job_title" class="admin-field-input w-full" value="{{ old('job_title', $contact->job_title) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="job_title"></p>
				</div>
				<div>
					<label class="admin-field-label" for="website">Website</label>
					<input id="website" name="website" class="admin-field-input w-full" value="{{ old('website', $contact->website) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="website"></p>
				</div>
			</div>

			<div class="grid gap-4 sm:grid-cols-2">
				<div>
					<label class="admin-field-label" for="type">Type</label>
					<select id="type" name="type" class="admin-field-input w-full">
						@foreach (App\Models\CrmContact::TYPES as $t)
							<option value="{{ $t }}" @selected(old('type', $contact->type) === $t)>{{ $t }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="type"></p>
				</div>
				<div>
					<label class="admin-field-label" for="status">Status</label>
					<select id="status" name="status" class="admin-field-input w-full">
						@foreach (App\Models\CrmContact::STATUSES as $t)
							<option value="{{ $t }}" @selected(old('status', $contact->status) === $t)>{{ $t }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="status"></p>
				</div>
				<div>
					<label class="admin-field-label" for="priority">Priority</label>
					<select id="priority" name="priority" class="admin-field-input w-full">
						@foreach (App\Models\CrmContact::PRIORITIES as $t)
							<option value="{{ $t }}" @selected(old('priority', $contact->priority) === $t)>{{ $t }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="priority"></p>
				</div>
				<div>
					<label class="admin-field-label" for="source_id">Source</label>
					<select id="source_id" name="source_id" class="admin-field-input w-full">
						<option value="">—</option>
						@foreach ($sources as $s)
							<option value="{{ $s->id }}" @selected((int) old('source_id', $contact->source_id) === $s->id)>{{ $s->name }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="source_id"></p>
				</div>
				<div>
					<label class="admin-field-label" for="source_detail">Source detail</label>
					<input id="source_detail" name="source_detail" class="admin-field-input w-full" value="{{ old('source_detail', $contact->source_detail) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="source_detail"></p>
				</div>
				<div>
					<label class="admin-field-label" for="service_interest">Service interest</label>
					<input id="service_interest" name="service_interest" class="admin-field-input w-full" value="{{ old('service_interest', $contact->service_interest) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="service_interest"></p>
				</div>
				<div>
					<label class="admin-field-label" for="service_id">Service (CMS)</label>
					<select id="service_id" name="service_id" class="admin-field-input w-full">
						<option value="">—</option>
						@foreach ($services as $svc)
							<option value="{{ $svc->id }}" @selected((int) old('service_id', $contact->service_id) === $svc->id)>{{ $svc->title }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="service_id"></p>
				</div>
				<div>
					<label class="admin-field-label" for="saas_platform_id">SaaS platform</label>
					<select id="saas_platform_id" name="saas_platform_id" class="admin-field-input w-full">
						<option value="">—</option>
						@foreach ($products as $p)
							<option value="{{ $p->id }}" @selected((int) old('saas_platform_id', $contact->saas_platform_id) === $p->id)>{{ $p->title }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="saas_platform_id"></p>
				</div>
				<div>
					<label class="admin-field-label" for="project_id">Running project</label>
					<select id="project_id" name="project_id" class="admin-field-input w-full">
						<option value="">—</option>
						@foreach ($projects as $pr)
							<option value="{{ $pr->id }}" @selected((int) old('project_id', $contact->project_id) === $pr->id)>{{ $pr->title }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="project_id"></p>
				</div>
				<div>
					<label class="admin-field-label" for="industry">Industry</label>
					<input id="industry" name="industry" class="admin-field-input w-full" value="{{ old('industry', $contact->industry) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="industry"></p>
				</div>
				<div>
					<label class="admin-field-label" for="company_size">Company size</label>
					<input id="company_size" name="company_size" class="admin-field-input w-full" value="{{ old('company_size', $contact->company_size) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="company_size"></p>
				</div>
				<div>
					<label class="admin-field-label" for="budget_range">Budget range</label>
					<input id="budget_range" name="budget_range" class="admin-field-input w-full" value="{{ old('budget_range', $contact->budget_range) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="budget_range"></p>
				</div>
				<div>
					<label class="admin-field-label" for="assigned_to">Assigned admin</label>
					<select id="assigned_to" name="assigned_to" class="admin-field-input w-full">
						<option value="">—</option>
						@foreach ($admins as $u)
							<option value="{{ $u->id }}" @selected((int) old('assigned_to', $contact->assigned_to) === $u->id)>{{ $u->name }}</option>
						@endforeach
					</select>
					<p class="mt-1 text-xs text-red-600" data-error-for="assigned_to"></p>
				</div>
				<div>
					<label class="admin-field-label" for="next_follow_up_at">Next follow-up</label>
					<input id="next_follow_up_at" name="next_follow_up_at" type="datetime-local" class="admin-field-input w-full" value="{{ old('next_follow_up_at', $contact->next_follow_up_at?->format('Y-m-d\TH:i')) }}">
					<p class="mt-1 text-xs text-red-600" data-error-for="next_follow_up_at"></p>
				</div>
			</div>

			<div>
				<label class="admin-field-label" for="notes">Notes</label>
				<textarea id="notes" name="notes" rows="4" class="admin-field-input w-full">{{ old('notes', $contact->notes) }}</textarea>
				<p class="mt-1 text-xs text-red-600" data-error-for="notes"></p>
			</div>

			<div class="flex gap-2">
				<x-admin.button type="submit">Save</x-admin.button>
				<x-admin.button variant="secondary" href="{{ route('admin.crm.contacts.index') }}">Cancel</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection
