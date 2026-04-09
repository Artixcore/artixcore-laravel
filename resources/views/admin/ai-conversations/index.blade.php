@extends('layouts.admin')

@section('title', 'AI conversations')

@section('content')
	<x-admin.page-header title="AI conversations">
		<x-slot:subtitle>Visitor chat threads tied to agents.</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Public ID</th>
					<th class="px-4 py-3 font-semibold">Agent</th>
					<th class="px-4 py-3 font-semibold">Lead</th>
					<th class="px-4 py-3 font-semibold">Last message</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@foreach ($conversations as $c)
					<tr class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-mono text-xs text-zinc-700">{{ \Illuminate\Support\Str::limit($c->public_id, 14) }}…</td>
						<td class="px-4 py-3 text-sm">{{ $c->agent?->name ?? '—' }}</td>
						<td class="px-4 py-3 text-sm">
							@if ($c->lead_id)
								<a href="{{ route('admin.leads.show', $c->lead_id) }}" class="text-indigo-600 hover:underline">#{{ $c->lead_id }}</a>
							@else
								—
							@endif
						</td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $c->last_message_at?->diffForHumans() ?? '—' }}</td>
						<td class="px-4 py-3 text-right">
							<x-admin.button variant="ghost" :href="route('admin.ai-conversations.show', $c)">View</x-admin.button>
						</td>
					</tr>
				@endforeach
			</tbody>
		</x-admin.table>
		<div class="border-t border-zinc-100 px-4 py-3">{{ $conversations->links() }}</div>
	</x-admin.card>
@endsection
