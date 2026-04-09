@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New AI agent' : 'Edit AI agent')

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New AI agent' : 'Edit AI agent'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.ai-agents.store') : route('admin.ai-agents.update', $agent) }}"
			class="space-y-6"
			id="resource-form"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<h3 class="text-sm font-semibold text-zinc-900">Identity</h3>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="name" label="Name" value="{{ old('name', $agent->name) }}" required />
				<x-admin.input name="slug" label="Slug" value="{{ old('slug', $agent->slug) }}" required />
			</div>
			<x-admin.select name="status" label="Status" required>
				<option value="active" @selected(old('status', $agent->status) === 'active')>Active</option>
				<option value="disabled" @selected(old('status', $agent->status) === 'disabled')>Disabled</option>
			</x-admin.select>
			<x-admin.select name="focus" label="Focus" required>
				<option value="general" @selected(old('focus', $agent->focus) === 'general')>General</option>
				<option value="sales" @selected(old('focus', $agent->focus) === 'sales')>Sales</option>
				<option value="support" @selected(old('focus', $agent->focus) === 'support')>Support</option>
			</x-admin.select>
			<x-admin.select name="default_ai_provider_id" label="Preferred provider (optional)">
				<option value="">— Auto (fallback chain) —</option>
				@foreach ($providers as $p)
					<option value="{{ $p->id }}" @selected((string) old('default_ai_provider_id', $agent->default_ai_provider_id) === (string) $p->id)>
						{{ $p->name ?: $p->driver }} (priority {{ $p->priority }})
					</option>
				@endforeach
			</x-admin.select>
			<x-admin.input name="model_id" label="Model override (optional)" value="{{ old('model_id', $agent->model_id) }}" />

			<h3 class="border-t border-zinc-100 pt-6 text-sm font-semibold text-zinc-900">Business context</h3>
			<x-admin.input name="role_label" label="Agent role label" value="{{ old('role_label', $agent->role_label) }}" />
			<x-admin.input name="business_name" label="Business name" value="{{ old('business_name', $agent->business_name) }}" />
			<x-admin.textarea name="business_description" label="Business description" rows="4">{{ old('business_description', $agent->business_description) }}</x-admin.textarea>
			<x-admin.textarea name="business_goals" label="Business goals" rows="3">{{ old('business_goals', $agent->business_goals) }}</x-admin.textarea>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="tone" label="Tone" value="{{ old('tone', $agent->tone) }}" />
				<x-admin.input name="response_style" label="Response style" value="{{ old('response_style', $agent->response_style) }}" />
			</div>
			<x-admin.textarea name="languages_json" label="Languages (JSON array)" rows="2" class="font-mono text-xs">{{ old('languages_json', json_encode($agent->languages ?? ['en'], JSON_UNESCAPED_SLASHES)) }}</x-admin.textarea>
			<x-admin.textarea name="forbidden_topics" label="Forbidden topics / boundaries" rows="3">{{ old('forbidden_topics', $agent->forbidden_topics) }}</x-admin.textarea>

			<h3 class="border-t border-zinc-100 pt-6 text-sm font-semibold text-zinc-900">Instructions &amp; automation</h3>
			<x-admin.textarea name="instructions" label="System instructions / prompt" rows="10">{{ old('instructions', $agent->instructions) }}</x-admin.textarea>
			<x-admin.textarea name="lead_capture_schema_json" label="Lead capture schema (JSON)" rows="4" class="font-mono text-xs">{{ old('lead_capture_schema_json', $agent->lead_capture_schema ? json_encode($agent->lead_capture_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{}') }}</x-admin.textarea>
			<x-admin.textarea name="escalation_rules_json" label="Escalation rules (JSON)" rows="3" class="font-mono text-xs">{{ old('escalation_rules_json', $agent->escalation_rules ? json_encode($agent->escalation_rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{}') }}</x-admin.textarea>
			<x-admin.textarea name="availability_json" label="Availability (JSON)" rows="3" class="font-mono text-xs">{{ old('availability_json', $agent->availability ? json_encode($agent->availability, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{}') }}</x-admin.textarea>
			<x-admin.textarea name="tools_allowed_json" label="Tools allowed (JSON)" rows="3" class="font-mono text-xs">{{ old('tools_allowed_json', $agent->tools_allowed ? json_encode($agent->tools_allowed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>

			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.ai-agents.index')">Cancel</x-admin.button>
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
						window.location = '{{ route('admin.ai-agents.index') }}';
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
