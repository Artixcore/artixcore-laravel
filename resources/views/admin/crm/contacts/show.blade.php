@extends('layouts.admin')

@section('title', $contact->name)

@section('content')
	<x-admin.page-header :title="$contact->name">
		<x-slot:subtitle>CRM contact &amp; timeline</x-slot:subtitle>
		<x-slot:actions>
			@can('update', $contact)
				<x-admin.button variant="secondary" href="{{ route('admin.crm.contacts.edit', $contact) }}">Edit</x-admin.button>
			@endcan
			<x-admin.button variant="secondary" href="{{ route('admin.crm.contacts.index') }}">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	@include('admin.crm._nav')

	<div class="mb-6 grid gap-4 lg:grid-cols-3">
		<x-admin.card class="p-4 lg:col-span-2">
			<dl class="grid gap-3 text-sm sm:grid-cols-2">
				<div><dt class="text-zinc-500">Email</dt><dd class="font-medium">{{ $contact->email ?: '—' }}</dd></div>
				<div><dt class="text-zinc-500">Phone</dt><dd>{{ $contact->phone ?: '—' }}</dd></div>
				<div><dt class="text-zinc-500">Company</dt><dd>{{ $contact->company_name ?: '—' }}</dd></div>
				<div><dt class="text-zinc-500">Status</dt><dd>{{ $contact->status }}</dd></div>
				<div><dt class="text-zinc-500">Priority</dt><dd>{{ $contact->priority }}</dd></div>
				<div><dt class="text-zinc-500">Source</dt><dd>{{ $contact->source?->name ?? '—' }}</dd></div>
				<div><dt class="text-zinc-500">Geo</dt><dd>{{ collect([$contact->geo_city, $contact->geo_region, $contact->geo_country])->filter()->join(', ') ?: '—' }}</dd></div>
				<div><dt class="text-zinc-500">IP</dt><dd class="font-mono text-xs">{{ $contact->ip_address ?: '—' }}</dd></div>
				@if ($contact->lead)
					<div class="sm:col-span-2">
						<dt class="text-zinc-500">Related lead</dt>
						<dd><a href="{{ route('admin.leads.show', $contact->lead) }}" class="text-indigo-600 hover:underline">Lead #{{ $contact->lead->id }}</a></dd>
					</div>
				@endif
			</dl>
		</x-admin.card>
		<x-admin.card class="p-4">
			<h2 class="text-sm font-semibold text-zinc-900">Quick status</h2>
			@can('update', $contact)
				<form id="crm-status-form" class="mt-3 space-y-2">
					@csrf
					<select name="status" class="admin-field-input w-full">
						@foreach (App\Models\CrmContact::STATUSES as $st)
							<option value="{{ $st }}" @selected($contact->status === $st)>{{ $st }}</option>
						@endforeach
					</select>
					<x-admin.button type="button" id="crm-status-btn" class="w-full">Update status</x-admin.button>
					<p class="text-xs text-red-600" data-error-for="status"></p>
				</form>
			@endcan
		</x-admin.card>
	</div>

	<div class="grid gap-6 lg:grid-cols-2">
		@can('email', $contact)
			<x-admin.card class="p-4">
				<h2 class="text-sm font-semibold text-zinc-900">Send email</h2>
				<form id="crm-email-form" class="mt-3 space-y-3">
					@csrf
					<div>
						<label class="admin-field-label" for="email-subject">Subject</label>
						<input id="email-subject" name="subject" class="admin-field-input w-full" required maxlength="190">
						<p class="mt-1 text-xs text-red-600" data-error-for="subject"></p>
					</div>
					<div>
						<label class="admin-field-label" for="email-template">Template (optional)</label>
						<select id="email-template" class="admin-field-input w-full">
							<option value="">—</option>
							@foreach ($emailTemplates as $tpl)
								<option value="{{ $tpl->id }}" data-subject="{{ e($tpl->subject) }}" data-body="{{ e($tpl->body) }}">{{ $tpl->name }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label" for="email-body">Message</label>
						<textarea id="email-body" name="body" rows="5" class="admin-field-input w-full" required></textarea>
						<p class="mt-1 text-xs text-red-600" data-error-for="body"></p>
					</div>
					<x-admin.button type="submit" id="crm-email-btn">Send</x-admin.button>
				</form>
			</x-admin.card>
		@endcan

		@can('update', $contact)
			<x-admin.card class="p-4">
				<h2 class="text-sm font-semibold text-zinc-900">Add note</h2>
				<form id="crm-note-form" class="mt-3 space-y-3">
					@csrf
					<div>
						<label class="admin-field-label" for="note-type">Type</label>
						<select id="note-type" name="type" class="admin-field-input w-full">
							@foreach (App\Models\CrmContactNote::TYPES as $t)
								@if ($t !== App\Models\CrmContactNote::TYPE_SYSTEM)
									<option value="{{ $t }}">{{ $t }}</option>
								@endif
							@endforeach
						</select>
						<p class="mt-1 text-xs text-red-600" data-error-for="type"></p>
					</div>
					<div>
						<label class="admin-field-label" for="note-title">Title (optional)</label>
						<input id="note-title" name="title" class="admin-field-input w-full">
						<p class="mt-1 text-xs text-red-600" data-error-for="title"></p>
					</div>
					<div>
						<label class="admin-field-label" for="note-body">Body</label>
						<textarea id="note-body" name="body" rows="4" class="admin-field-input w-full" required></textarea>
						<p class="mt-1 text-xs text-red-600" data-error-for="body"></p>
					</div>
					<x-admin.button type="submit" id="crm-note-btn">Add note</x-admin.button>
				</form>
			</x-admin.card>
		@endcan
	</div>

	<x-admin.card class="mt-6 p-4">
		<h2 class="text-sm font-semibold text-zinc-900">Timeline</h2>
		<ol class="mt-4 space-y-4 border-l border-zinc-200 pl-4">
			@foreach ($contact->notes as $note)
				<li class="relative">
					<span class="absolute -left-[21px] top-1.5 size-2.5 rounded-full bg-indigo-500 ring-4 ring-white"></span>
					<p class="text-xs text-zinc-500">{{ $note->created_at?->format('Y-m-d H:i') }} · {{ $note->type }} @if($note->user) · {{ $note->user->name }} @endif</p>
					@if ($note->title)
						<p class="text-sm font-medium text-zinc-900">{{ $note->title }}</p>
					@endif
					<p class="text-sm text-zinc-700">{{ $note->body }}</p>
				</li>
			@endforeach
		</ol>
	</x-admin.card>

	@push('scripts')
		<script>
			(function () {
				const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
				const contactUrl = @json(route('admin.crm.contacts.show', $contact));
				const statusUrl = @json(route('admin.crm.contacts.status', $contact));
				const emailUrl = @json(route('admin.crm.contacts.send-email', $contact));
				const noteUrl = @json(route('admin.crm.contacts.notes.store', $contact));

				function toast(msg, type) {
					window.adminToast?.(msg, type);
				}

				function clearErrors(root) {
					root?.querySelectorAll('[data-error-for]').forEach((el) => (el.textContent = ''));
				}

				function showErrors(root, errors) {
					if (!errors || typeof errors !== 'object') return;
					Object.keys(errors).forEach((field) => {
						const el = root?.querySelector(`[data-error-for="${field}"]`);
						if (el && Array.isArray(errors[field])) el.textContent = errors[field].join(' ');
					});
				}

				document.getElementById('crm-status-btn')?.addEventListener('click', async () => {
					const form = document.getElementById('crm-status-form');
					const fd = new FormData(form);
					fd.append('_method', 'PATCH');
					clearErrors(form);
					try {
						const res = await fetch(statusUrl, {
							method: 'POST',
							headers: {
								Accept: 'application/json',
								'X-Requested-With': 'XMLHttpRequest',
								'X-CSRF-TOKEN': csrf,
							},
							body: fd,
						});
						const data = await res.json().catch(() => ({}));
						if (res.status === 422) {
							showErrors(form, data.errors);
							toast(data.message || 'Validation failed', 'error');
							return;
						}
						if (!res.ok) {
							toast(data.message || 'Failed', 'error');
							return;
						}
						toast(data.message || 'Updated');
						window.location.reload();
					} catch {
						toast('Network error', 'error');
					}
				});

				document.getElementById('email-template')?.addEventListener('change', (e) => {
					const opt = e.target.selectedOptions[0];
					if (!opt || !opt.value) return;
					const subj = document.getElementById('email-subject');
					const body = document.getElementById('email-body');
					if (subj) subj.value = opt.getAttribute('data-subject') || '';
					if (body) body.value = opt.getAttribute('data-body') || '';
				});

				document.getElementById('crm-email-form')?.addEventListener('submit', async (e) => {
					e.preventDefault();
					const form = e.target;
					clearErrors(form);
					const fd = new FormData(form);
					const btn = document.getElementById('crm-email-btn');
					btn.disabled = true;
					try {
						const res = await fetch(emailUrl, {
							method: 'POST',
							headers: {
								Accept: 'application/json',
								'X-Requested-With': 'XMLHttpRequest',
								'X-CSRF-TOKEN': csrf,
							},
							body: fd,
						});
						const data = await res.json().catch(() => ({}));
						if (res.status === 422) {
							showErrors(form, data.errors);
							toast(data.message || 'Validation failed', 'error');
							return;
						}
						if (!res.ok) {
							toast(data.message || 'Failed', 'error');
							return;
						}
						toast(data.message || 'Sent');
						window.location.reload();
					} catch {
						toast('Network error', 'error');
					} finally {
						btn.disabled = false;
					}
				});

				document.getElementById('crm-note-form')?.addEventListener('submit', async (e) => {
					e.preventDefault();
					const form = e.target;
					clearErrors(form);
					const fd = new FormData(form);
					const btn = document.getElementById('crm-note-btn');
					btn.disabled = true;
					try {
						const res = await fetch(noteUrl, {
							method: 'POST',
							headers: {
								Accept: 'application/json',
								'X-Requested-With': 'XMLHttpRequest',
								'X-CSRF-TOKEN': csrf,
							},
							body: fd,
						});
						const data = await res.json().catch(() => ({}));
						if (res.status === 422) {
							showErrors(form, data.errors);
							toast(data.message || 'Validation failed', 'error');
							return;
						}
						if (!res.ok) {
							toast(data.message || 'Failed', 'error');
							return;
						}
						toast(data.message || 'Saved');
						window.location.reload();
					} catch {
						toast('Network error', 'error');
					} finally {
						btn.disabled = false;
					}
				});
			})();
		</script>
	@endpush
@endsection
