@extends('layouts.admin')

@php
	use App\Models\AiProvider;
@endphp

@section('title', $mode === 'create' ? 'New AI provider' : 'Edit AI provider')

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New AI provider' : 'Edit AI provider'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.ai-providers.store') : route('admin.ai-providers.update', $provider) }}"
			class="space-y-6"
			id="resource-form"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="name" label="Display name" value="{{ old('name', $provider->name) }}" />
				<x-admin.select name="driver" label="Driver" required>
					<option value="{{ AiProvider::DRIVER_OPENAI }}" @selected(old('driver', $provider->driver) === AiProvider::DRIVER_OPENAI)>OpenAI</option>
					<option value="{{ AiProvider::DRIVER_GEMINI }}" @selected(old('driver', $provider->driver) === AiProvider::DRIVER_GEMINI)>Google Gemini</option>
					<option value="{{ AiProvider::DRIVER_GROK }}" @selected(old('driver', $provider->driver) === AiProvider::DRIVER_GROK)>Grok (xAI)</option>
					<option value="{{ AiProvider::DRIVER_CUSTOM }}" @selected(old('driver', $provider->driver) === AiProvider::DRIVER_CUSTOM)>Custom (OpenAI-compatible)</option>
				</x-admin.select>
			</div>
			<div class="rounded-lg border border-zinc-200 bg-zinc-50/80 p-4">
				<input type="hidden" name="is_enabled" value="0" />
				<label class="flex cursor-pointer items-center gap-3">
					<input type="checkbox" name="is_enabled" value="1" class="size-4 rounded border-zinc-300" @checked(old('is_enabled', $provider->is_enabled ?? true)) />
					<span class="text-sm font-medium text-zinc-800">Provider enabled</span>
				</label>
			</div>
			<x-admin.input
				name="api_key"
				label="API key (leave blank to keep existing)"
				type="password"
				autocomplete="new-password"
				value=""
				placeholder="{{ $mode === 'edit' && $provider->api_key_hint ? '••••'.$provider->api_key_hint : '' }}"
			/>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="default_model" label="Default model" value="{{ old('default_model', $provider->default_model) }}" placeholder="gpt-4o-mini / gemini-1.5-flash" />
				<x-admin.input name="base_url" label="Base URL (optional)" value="{{ old('base_url', $provider->base_url) }}" />
			</div>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<x-admin.input name="timeout_seconds" label="Timeout (seconds)" type="number" value="{{ old('timeout_seconds', $provider->timeout_seconds ?? 60) }}" required />
				<x-admin.input name="priority" label="Fallback priority (lower = tried first)" type="number" value="{{ old('priority', $provider->priority ?? 100) }}" required />
				<x-admin.input name="max_output_tokens" label="Max output tokens" type="number" value="{{ old('max_output_tokens', $provider->max_output_tokens) }}" />
			</div>
			<x-admin.textarea name="rate_limit_json" label="Rate limit JSON (optional)" rows="3" class="font-mono text-xs">{{ old('rate_limit_json', $provider->rate_limit_json ? json_encode($provider->rate_limit_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>
			<x-admin.textarea name="metadata_json" label="Metadata JSON (optional)" rows="3" class="font-mono text-xs">{{ old('metadata_json', $provider->metadata ? json_encode($provider->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>
			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.ai-providers.index')">Cancel</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection

@push('scripts')
	<script>
		$('#resource-form').on('submit', function (e) {
			e.preventDefault();
			var $f = $(this);
			$.ajax({
				url: $f.attr('action'),
				type: 'POST',
				data: $f.serialize(),
				headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
				success: function (res) {
					adminToast(res.message || 'Saved.', 'success');
					setTimeout(function () {
						window.location = '{{ route('admin.ai-providers.index') }}';
					}, 600);
				},
				error: function (xhr) {
					var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not save.';
					adminToast(msg, 'error');
				},
			});
		});
	</script>
@endpush
