@extends('layouts.admin')

@section('title', 'Security settings')

@section('content')
	<x-admin.page-header title="Security settings">
		<x-slot:subtitle>Chat rate limits and production readiness notes.</x-slot:subtitle>
	</x-admin.page-header>

	<div class="mb-6 grid gap-4 sm:grid-cols-2">
		<x-admin.card>
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Runtime</p>
			<ul class="mt-3 space-y-2 text-sm text-zinc-700">
				<li><span class="text-zinc-500">APP_DEBUG</span> — <strong class="{{ $appDebug ? 'text-amber-700' : 'text-emerald-700' }}">{{ $appDebug ? 'true (disable in production)' : 'false' }}</strong></li>
				<li><span class="text-zinc-500">APP_URL</span> — <code class="text-xs">{{ config('app.url') }}</code></li>
				<li><span class="text-zinc-500">Session lifetime</span> — {{ $sessionLifetime }} minutes</li>
			</ul>
		</x-admin.card>
		<x-admin.card>
			<p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">AI chat</p>
			<p class="mt-3 text-sm text-zinc-600">Public API: <code class="text-xs">POST /api/v1/ai/chat</code> (throttled per security limits below).</p>
		</x-admin.card>
	</div>

	<x-admin.card>
		<form method="post" action="{{ route('admin.security-settings.update') }}" id="security-form" class="space-y-6">
			@csrf
			@method('PUT')
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="chat_rate_limit_per_minute" label="Chat requests / minute (per IP + visitor token)" type="number" value="{{ old('chat_rate_limit_per_minute', $security->chat_rate_limit_per_minute) }}" required />
				<x-admin.input name="chat_rate_limit_per_day" label="Chat requests / day (per IP + visitor token)" type="number" value="{{ old('chat_rate_limit_per_day', $security->chat_rate_limit_per_day) }}" required />
			</div>
			<x-admin.textarea name="internal_notes" label="Internal notes (admin only)" rows="4">{{ old('internal_notes', $security->internal_notes) }}</x-admin.textarea>
			<x-admin.button variant="primary" type="submit">Save</x-admin.button>
		</form>
	</x-admin.card>
@endsection

@push('scripts')
	<script>
		$('#security-form').on('submit', function (e) {
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
