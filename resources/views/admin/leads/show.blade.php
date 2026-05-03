@extends('layouts.admin')

@section('title', 'Lead #'.$lead->id)

@section('content')
	<x-admin.page-header :title="'Lead #'.$lead->id">
		<x-slot:actions>
			<x-admin.dropdown-menu>
				<x-admin.dropdown-link
					danger
					data-admin-delete="{{ route('admin.leads.destroy', $lead) }}"
				>Delete</x-admin.dropdown-link>
			</x-admin.dropdown-menu>
			<x-admin.button variant="ghost" :href="route('admin.leads.index')">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<div class="grid gap-6 lg:grid-cols-2">
		<x-admin.card>
			<h3 class="mb-4 text-sm font-semibold text-zinc-900">Details</h3>
			<form method="post" action="{{ route('admin.leads.update', $lead) }}" id="lead-form" class="space-y-4" data-admin-ajax-form>
				@csrf
				@method('PUT')
				<x-admin.select name="status" label="Status" required>
					@foreach (\App\Models\Lead::statuses() as $st)
						<option value="{{ $st }}" @selected(old('status', $lead->status) === $st)>{{ ucfirst($st) }}</option>
					@endforeach
				</x-admin.select>
				<x-admin.select name="assigned_to" label="Assigned to">
					<option value="">— Unassigned —</option>
					@foreach ($staff as $u)
						<option value="{{ $u->id }}" @selected((string) old('assigned_to', $lead->assigned_to) === (string) $u->id)>{{ $u->name }}</option>
					@endforeach
				</x-admin.select>
				<x-admin.textarea name="admin_notes" label="Admin notes" rows="4">{{ old('admin_notes', $lead->admin_notes) }}</x-admin.textarea>
				<x-admin.input
					name="reviewed_at"
					label="Reviewed at"
					type="datetime-local"
					value="{{ old('reviewed_at', $lead->reviewed_at?->format('Y-m-d\TH:i')) }}"
				/>
				<x-admin.select name="reviewed_by" label="Reviewed by">
					<option value="">— —</option>
					@foreach ($staff as $u)
						<option value="{{ $u->id }}" @selected((string) old('reviewed_by', $lead->reviewed_by) === (string) $u->id)>{{ $u->name }}</option>
					@endforeach
				</x-admin.select>
				<div class="grid grid-cols-1 gap-3 md:grid-cols-2">
					<div class="md:col-span-2 rounded-lg border border-zinc-100 bg-zinc-50/50 px-3 py-2 text-sm">
						<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Source</p>
						<p class="mt-1 font-medium text-zinc-800">{{ $lead->source ?: '—' }}</p>
					</div>
					@if ($lead->service_type || $lead->message || $lead->submitted_at)
						<div class="md:col-span-2 rounded-lg border border-zinc-100 bg-white px-3 py-3 text-sm">
							<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Website submission</p>
							@if ($lead->service_type)
								<p class="mt-2"><span class="text-zinc-500">Service type:</span> {{ $lead->service_type }}</p>
							@endif
							@if ($lead->submitted_at)
								<p class="mt-1"><span class="text-zinc-500">Submitted:</span> {{ $lead->submitted_at->format('M j, Y g:i a') }}</p>
							@endif
							@if ($lead->message)
								<p class="mt-3 text-xs font-semibold uppercase tracking-wide text-zinc-500">Message</p>
								<p class="mt-1 whitespace-pre-wrap text-zinc-800">{{ $lead->message }}</p>
							@endif
							@if ($lead->ip_address)
								<p class="mt-3 font-mono text-xs text-zinc-600">IP: {{ $lead->ip_address }}</p>
							@endif
							@if ($lead->user_agent)
								<p class="mt-1 font-mono text-xs text-zinc-600 break-all">UA: {{ $lead->user_agent }}</p>
							@endif
						</div>
					@endif
					@if ($lead->visitor_context)
						<div class="md:col-span-2 rounded-lg border border-zinc-100 bg-zinc-50/50 px-3 py-2">
							<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Visitor context</p>
							<pre class="mt-2 max-h-48 overflow-auto whitespace-pre-wrap break-words font-mono text-xs text-zinc-700">{{ json_encode($lead->visitor_context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
						</div>
					@endif
					<x-admin.input name="name" label="Name" value="{{ old('name', $lead->name) }}" />
					<x-admin.input name="email" label="Email" type="email" value="{{ old('email', $lead->email) }}" />
					<x-admin.input name="phone" label="Phone" value="{{ old('phone', $lead->phone) }}" />
					<x-admin.input name="company" label="Company" value="{{ old('company', $lead->company) }}" />
				</div>
				<x-admin.input name="budget" label="Budget" value="{{ old('budget', $lead->budget) }}" />
				<x-admin.input name="service_interest" label="Service interest" value="{{ old('service_interest', $lead->service_interest) }}" />
				<x-admin.textarea name="notes" label="Notes" rows="3">{{ old('notes', $lead->notes) }}</x-admin.textarea>
				<x-admin.textarea name="conversation_summary" label="Conversation summary" rows="4">{{ old('conversation_summary', $lead->conversation_summary) }}</x-admin.textarea>
				<x-admin.textarea name="internal_notes" label="Internal notes" rows="4">{{ old('internal_notes', $lead->internal_notes) }}</x-admin.textarea>
				<x-admin.button variant="primary" type="submit">Save changes</x-admin.button>
			</form>
		</x-admin.card>

		<x-admin.card :noPadding="true">
			<div class="border-b border-zinc-100 px-4 py-3 text-sm font-semibold text-zinc-900">Conversation</div>
			@if ($lead->conversation)
				<div class="px-4 py-2 text-xs">
					<a href="{{ route('admin.ai-conversations.show', $lead->conversation) }}" class="text-indigo-600 hover:underline">Open thread</a>
				</div>
				<ul class="max-h-[32rem] divide-y divide-zinc-100 overflow-y-auto">
					@foreach ($lead->conversation->messages as $msg)
						<li class="px-4 py-3">
							<p class="text-xs font-semibold text-zinc-500">{{ $msg->role }}</p>
							<pre class="mt-1 whitespace-pre-wrap font-sans text-sm text-zinc-800">{{ $msg->content }}</pre>
						</li>
					@endforeach
				</ul>
			@else
				<p class="px-4 py-8 text-center text-sm text-zinc-500">No linked conversation.</p>
			@endif
		</x-admin.card>
	</div>
@endsection
