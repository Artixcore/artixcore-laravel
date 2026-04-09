@extends('layouts.admin')

@section('title', 'Conversation')

@section('content')
	<x-admin.page-header title="AI conversation">
		<x-slot:subtitle>Public ID: <code class="text-xs">{{ $conversation->public_id }}</code></x-slot:subtitle>
		<x-slot:actions>
			@if ($conversation->lead_id)
				<x-admin.button variant="secondary" :href="route('admin.leads.show', $conversation->lead_id)">Open lead</x-admin.button>
			@endif
			<x-admin.button variant="ghost" :href="route('admin.ai-conversations.index')">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card class="mb-6">
		<dl class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
			<div><span class="text-zinc-500">Agent</span><p class="font-medium">{{ $conversation->agent?->name ?? '—' }}</p></div>
			<div><span class="text-zinc-500">Channel</span><p class="font-medium">{{ $conversation->channel }}</p></div>
			<div><span class="text-zinc-500">Status</span><p class="font-medium">{{ $conversation->status }}</p></div>
			<div><span class="text-zinc-500">Last message</span><p class="font-medium">{{ $conversation->last_message_at?->toIso8601String() ?? '—' }}</p></div>
		</dl>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<div class="border-b border-zinc-100 px-4 py-3 text-sm font-semibold text-zinc-900">Messages</div>
		<ul class="divide-y divide-zinc-100">
			@forelse ($conversation->messages as $msg)
				<li class="px-4 py-4">
					<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ $msg->role }}
						@if ($msg->provider_driver)
							<span class="font-normal normal-case text-zinc-400">· {{ $msg->provider_driver }}</span>
						@endif
					</p>
					<pre class="mt-2 whitespace-pre-wrap font-sans text-sm text-zinc-800">{{ $msg->content }}</pre>
				</li>
			@empty
				<li class="px-4 py-8 text-center text-sm text-zinc-500">No messages.</li>
			@endforelse
		</ul>
	</x-admin.card>
@endsection
