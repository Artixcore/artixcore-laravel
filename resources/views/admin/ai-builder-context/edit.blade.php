@extends('layouts.admin')

@section('title', 'AI builder context')

@section('content')
	<x-admin.page-header title="AI builder context">
		<x-slot:subtitle>Business profile used by the visual page builder assistant.</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card>
		<form method="post" action="{{ route('admin.ai-builder-context.update') }}" id="ai-builder-context-form" class="grid grid-cols-1 gap-4 md:grid-cols-2">
			@csrf
			@method('PUT')
			<x-admin.input name="business_name" label="Business name" value="{{ old('business_name', $profile->business_name) }}" />
			<x-admin.input name="business_type" label="Business type" value="{{ old('business_type', $profile->business_type) }}" />
			<div class="md:col-span-2">
				<x-admin.textarea name="brand_summary" label="Brand summary" rows="3">{{ old('brand_summary', $profile->brand_summary) }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="target_audience" label="Target audience" rows="3">{{ old('target_audience', $profile->target_audience) }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="main_services" label="Main services or products" rows="3">{{ old('main_services', $profile->main_services) }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="unique_selling_points" label="Unique selling points" rows="3">{{ old('unique_selling_points', $profile->unique_selling_points) }}</x-admin.textarea>
			</div>
			<x-admin.input name="tone_of_voice" label="Tone of voice" value="{{ old('tone_of_voice', $profile->tone_of_voice) }}" />
			<x-admin.input name="writing_style" label="Writing style" value="{{ old('writing_style', $profile->writing_style) }}" />
			<x-admin.input name="preferred_cta_goal" label="Preferred CTA goal" value="{{ old('preferred_cta_goal', $profile->preferred_cta_goal) }}" />
			<x-admin.input name="location" label="Location" value="{{ old('location', $profile->location) }}" />
			<div class="md:col-span-2">
				<x-admin.textarea name="offer_details" label="Offer details" rows="3">{{ old('offer_details', $profile->offer_details) }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="forbidden_topics" label="Forbidden topics / blocked claims" rows="3">{{ old('forbidden_topics', $profile->forbidden_topics) }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="style_notes" label="Style notes" rows="2">{{ old('style_notes', $profile->style_notes) }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="contact_details_json" label="Contact details (JSON object)" rows="3">{{ old('contact_details_json', $profile->contact_details ? json_encode($profile->contact_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.textarea name="brand_colors_json" label="Brand colors (JSON)" rows="3">{{ old('brand_colors_json', $profile->brand_colors ? json_encode($profile->brand_colors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</x-admin.textarea>
			</div>
			<div class="md:col-span-2">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection

@push('scripts')
	<script>
		$('#ai-builder-context-form').on('submit', function (e) {
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
