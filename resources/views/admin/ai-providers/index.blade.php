@extends('layouts.admin')

@section('title', 'AI providers')

@section('content')
	<x-admin.page-header title="AI providers">
		<x-slot:subtitle>Connect OpenAI, Gemini, Grok, or custom OpenAI-compatible endpoints.</x-slot:subtitle>
		<x-slot:actions>
			<x-admin.button variant="primary" :href="route('admin.ai-providers.create')">Add provider</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card :noPadding="true">
		<x-admin.table>
			<thead class="border-b border-zinc-100 bg-zinc-50/90 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
				<tr>
					<th class="px-4 py-3 font-semibold">Name</th>
					<th class="px-4 py-3 font-semibold">Driver</th>
					<th class="px-4 py-3 font-semibold">Model</th>
					<th class="px-4 py-3 font-semibold">Priority</th>
					<th class="px-4 py-3 font-semibold">Key</th>
					<th class="px-4 py-3 font-semibold">Enabled</th>
					<th class="w-px px-4 py-3 font-semibold"><span class="sr-only">Actions</span></th>
				</tr>
			</thead>
			<tbody class="divide-y divide-zinc-100 bg-white">
				@forelse ($providers as $provider)
					<tr class="transition hover:bg-zinc-50/80">
						<td class="px-4 py-3 font-medium text-zinc-900">{{ $provider->name ?: '—' }}</td>
						<td class="px-4 py-3"><code class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs">{{ $provider->driver }}</code></td>
						<td class="px-4 py-3 text-sm text-zinc-600">{{ $provider->default_model ?: '—' }}</td>
						<td class="px-4 py-3 text-sm">{{ $provider->priority }}</td>
						<td class="px-4 py-3 text-sm">{{ $provider->api_key_hint ? '••••'.$provider->api_key_hint : '—' }}</td>
						<td class="px-4 py-3">
							@if ($provider->is_enabled)
								<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-800">On</span>
							@else
								<span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600">Off</span>
							@endif
						</td>
						<td class="px-4 py-3 text-right">
							<x-admin.dropdown-menu>
								<x-admin.dropdown-link :href="route('admin.ai-providers.edit', $provider)">Edit</x-admin.dropdown-link>
								<x-admin.dropdown-link
									danger
									data-admin-delete="{{ route('admin.ai-providers.destroy', $provider) }}"
								>Delete</x-admin.dropdown-link>
							</x-admin.dropdown-menu>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" class="px-4 py-8 text-center text-sm text-zinc-500">No providers yet.</td>
					</tr>
				@endforelse
			</tbody>
		</x-admin.table>
	</x-admin.card>
@endsection
