@extends('layouts.master')

@section('title', 'IP allowlist')
@section('topbar_title', 'IP allowlist')

@section('content')
	<x-admin.page-header title="Access control">
		<x-slot:subtitle>Restrict admin and master login by IP or CIDR (IPv4/IPv6)</x-slot:subtitle>
	</x-admin.page-header>

	<p class="mb-6 text-sm text-zinc-600">
		MAC address filtering is not available for public web apps. Use IP allowlisting and (when enabled) two-factor authentication.
	</p>

	<x-admin.card class="mb-8">
		<h3 class="text-sm font-semibold text-zinc-900">Add rule</h3>
		<form method="post" action="{{ route('master.security.ip-rules.store') }}" class="mt-4 grid gap-4 md:grid-cols-2" data-master-ip-form>
			@csrf
			<x-admin.input name="name" label="Label (optional)" />
			<x-admin.select name="guard_area" label="Area" required>
				<option value="admin">Admin</option>
				<option value="master">Master</option>
				<option value="both">Both</option>
			</x-admin.select>
			<x-admin.input name="ip_address" label="Single IP" placeholder="203.0.113.10" />
			<x-admin.input name="cidr" label="CIDR" placeholder="203.0.113.0/24" />
			<div class="md:col-span-2">
				<x-admin.textarea name="description" label="Description" rows="2">{{ old('description') }}</x-admin.textarea>
			</div>
			<div class="flex items-center gap-2 md:col-span-2">
				<input type="hidden" name="is_active" value="0">
				<input type="checkbox" name="is_active" id="is_active_new" value="1" checked class="size-4 rounded border-zinc-300">
				<label for="is_active_new" class="text-sm text-zinc-700">Active</label>
			</div>
			<div class="md:col-span-2">
				<x-admin.button variant="primary" type="submit">Create rule</x-admin.button>
			</div>
		</form>
	</x-admin.card>

	<x-admin.card :noPadding="true">
		<div class="border-b border-zinc-100 px-4 py-3 text-sm font-semibold text-zinc-900">Rules</div>
		<div class="overflow-x-auto">
			<table class="min-w-full text-left text-sm">
				<thead class="border-b border-zinc-100 bg-zinc-50 text-xs uppercase text-zinc-500">
					<tr>
						<th class="px-4 py-2">Area</th>
						<th class="px-4 py-2">Match</th>
						<th class="px-4 py-2">Active</th>
						<th class="px-4 py-2"></th>
					</tr>
				</thead>
				<tbody class="divide-y divide-zinc-100">
					@foreach ($rules as $rule)
						<tr>
							<td class="px-4 py-2 font-medium text-zinc-800">{{ $rule->guard_area }}</td>
							<td class="px-4 py-2 font-mono text-xs text-zinc-700">{{ $rule->cidr ?: $rule->ip_address }}</td>
							<td class="px-4 py-2">{{ $rule->is_active ? 'Yes' : 'No' }}</td>
							<td class="px-4 py-2 text-right">
								<form method="post" action="{{ route('master.security.ip-rules.destroy', $rule) }}" class="inline" data-master-ip-form>
									@csrf
									@method('DELETE')
									<button type="submit" class="text-xs font-medium text-red-600 hover:underline">Delete</button>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</x-admin.card>
@endsection
