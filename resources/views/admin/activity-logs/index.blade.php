@extends('layouts.admin')

@section('title', 'Audit log')

@section('content')
	<x-admin.page-header title="Audit log">
		<x-slot:subtitle>Sensitive admin actions and configuration changes.</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card class="mb-4">
		<form method="get" class="flex flex-wrap items-end gap-3">
			<x-admin.input name="action" label="Action contains" value="{{ $currentAction }}" />
			<div>
				<label class="admin-field-label" for="actor">Actor</label>
				<select name="actor_id" id="actor" class="admin-field-input min-w-[12rem]">
					<option value="">Any</option>
					@foreach ($actors as $a)
						<option value="{{ $a->id }}" @selected($currentActorId === $a->id)>{{ $a->name }}</option>
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
					<th class="px-4 py-3 font-semibold">When</th>
					<th class="px-4 py-3 font-semibold">Action</th>
					<th class="px-4 py-3 font-semibold">Actor</th>
					<th class="px-4 py-3 font-semibold">Subject</th>
					<th class="px-4 py-3 font-semibold">IP</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($logs as $log)
					<tr class="align-top">
						<td class="px-4 py-3 text-xs text-zinc-600">{{ $log->created_at?->toIso8601String() }}</td>
						<td class="px-4 py-3 font-mono text-xs text-zinc-900">{{ $log->action }}</td>
						<td class="px-4 py-3 text-sm">{{ $log->actor?->name ?? '—' }}</td>
						<td class="px-4 py-3 text-xs text-zinc-600">
							@if ($log->subject_type)
								{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
							@else
								—
							@endif
						</td>
						<td class="px-4 py-3 font-mono text-xs text-zinc-500">{{ $log->ip_address ?? '—' }}</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $logs->links() }}</div>
	</x-admin.card>
@endsection
