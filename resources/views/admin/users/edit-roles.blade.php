@extends('layouts.admin')

@section('title', 'User roles — '.$user->name)

@section('content')
	<x-admin.page-header :title="'Roles: '.$user->name">
		<x-slot:subtitle>{{ $user->email }}</x-slot:subtitle>
		<x-slot:actions>
			<x-admin.button variant="ghost" :href="route('admin.users.index')">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	@if (! $canManageRoles)
		<x-admin.card>
			<p class="text-sm text-zinc-600">You do not have permission to change roles for this user.</p>
		</x-admin.card>
	@else
		<x-admin.card>
			<form method="post" action="{{ route('admin.users.roles', $user) }}" id="roles-form" class="space-y-4">
				@csrf
				@method('PUT')
				<p class="text-sm text-zinc-600">Select one or more roles (web guard).</p>
				<div class="space-y-2">
					@foreach ($roles as $role)
						<label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-100 px-3 py-2 hover:bg-zinc-50">
							<input
								type="checkbox"
								name="roles[]"
								value="{{ $role->name }}"
								class="size-4 rounded border-zinc-300"
								@checked($user->roles->contains('name', $role->name))
							/>
							<span class="text-sm font-medium text-zinc-800">{{ $role->name }}</span>
						</label>
					@endforeach
				</div>
				<x-admin.button variant="primary" type="submit">Save roles</x-admin.button>
			</form>
		</x-admin.card>
	@endif
@endsection

@push('scripts')
	<script>
		$('#roles-form').on('submit', function (e) {
			e.preventDefault();
			var $f = $(this);
			$.ajax({
				url: $f.attr('action'),
				type: 'POST',
				data: $f.serialize(),
				headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
				success: function (res) {
					adminToast(res.message || 'Saved.', 'success');
				},
				error: function (xhr) {
					var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not save.';
					adminToast(msg, 'error');
				},
			});
		});
	</script>
@endpush
