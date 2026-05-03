@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New market update' : 'Edit market update')

@php
	$categoryTerms = $marketUpdate->exists
		? $marketUpdate->terms->filter(fn ($t) => ($t->taxonomy?->slug ?? '') === 'categories')
		: collect();
	$selectedCategoryIds = old('category_term_ids', $categoryTerms->pluck('id')->all());
	$selectedTagIds = old('tag_term_ids', $marketUpdate->exists ? $marketUpdate->terms->filter(fn ($t) => ($t->taxonomy?->slug ?? '') === 'tags')->pluck('id')->all() : []);
	$videoEmbed = $marketUpdate->exists ? $marketUpdate->video_embed : null;
	$sourceUrlsOld = old('source_urls_json', $marketUpdate->exists && is_array($marketUpdate->source_urls) ? json_encode($marketUpdate->source_urls, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '');
@endphp

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New market update' : 'Edit market update'">
		<x-slot:actions>
			@if ($marketUpdate->exists)
				<x-admin.button variant="ghost" :href="route('admin.market-updates.preview', $marketUpdate)" target="_blank">Preview</x-admin.button>
			@endif
			<x-admin.button variant="ghost" :href="route('admin.market-updates.index')">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.market-updates.store') : route('admin.market-updates.update', $marketUpdate) }}"
			id="resource-form"
			class="space-y-8"
			enctype="multipart/form-data"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif

			<div class="space-y-4">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Content</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="title" label="Title" value="{{ old('title', $marketUpdate->title) }}" required />
					<x-admin.input name="slug" label="Slug" value="{{ old('slug', $marketUpdate->slug) }}" />
				</div>
				@if ($marketUpdate->slug_locked)
					<label class="inline-flex items-center gap-2 text-sm text-zinc-700">
						<input type="checkbox" name="unlock_slug" value="1" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(old('unlock_slug')) />
						Allow slug edit (unlock)
					</label>
				@endif
				<x-admin.input name="market_area" label="Market area" value="{{ old('market_area', $marketUpdate->market_area) }}" />
				<x-admin.textarea name="excerpt" label="Excerpt" rows="3">{{ old('excerpt', $marketUpdate->excerpt) }}</x-admin.textarea>
				<x-admin.textarea name="trend_summary" label="Trend summary (HTML)" rows="6" class="font-mono text-xs">{{ old('trend_summary', $marketUpdate->trend_summary) }}</x-admin.textarea>
				<x-admin.textarea name="business_impact" label="Business impact (HTML)" rows="5" class="font-mono text-xs">{{ old('business_impact', $marketUpdate->business_impact) }}</x-admin.textarea>
				<x-admin.textarea name="technology_impact" label="Technology impact (HTML)" rows="5" class="font-mono text-xs">{{ old('technology_impact', $marketUpdate->technology_impact) }}</x-admin.textarea>
				<x-admin.textarea name="opportunities" label="Opportunities (HTML)" rows="4" class="font-mono text-xs">{{ old('opportunities', $marketUpdate->opportunities) }}</x-admin.textarea>
				<x-admin.textarea name="risks" label="Risks (HTML)" rows="4" class="font-mono text-xs">{{ old('risks', $marketUpdate->risks) }}</x-admin.textarea>
				<x-admin.textarea name="what_next" label="What businesses should explore next (HTML)" rows="4" class="font-mono text-xs">{{ old('what_next', $marketUpdate->what_next) }}</x-admin.textarea>
				<x-admin.textarea name="body" label="Full narrative body (HTML)" rows="10" class="font-mono text-xs">{{ old('body', $marketUpdate->body) }}</x-admin.textarea>
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Publishing</h3>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
					<x-admin.select name="status" label="Status">
						@foreach ([
							\App\Models\MarketUpdate::STATUS_DRAFT => 'Draft',
							\App\Models\MarketUpdate::STATUS_PENDING_REVIEW => 'Pending review',
							\App\Models\MarketUpdate::STATUS_SCHEDULED => 'Scheduled',
							\App\Models\MarketUpdate::STATUS_PUBLISHED => 'Published',
							\App\Models\MarketUpdate::STATUS_ARCHIVED => 'Archived',
						] as $val => $label)
							<option value="{{ $val }}" @selected(old('status', $marketUpdate->status ?: \App\Models\MarketUpdate::STATUS_DRAFT) === $val)>{{ $label }}</option>
						@endforeach
					</x-admin.select>
					<div>
						<label for="published_at" class="admin-field-label">Published at</label>
						<input id="published_at" type="datetime-local" name="published_at" class="admin-field-input" value="{{ old('published_at', optional($marketUpdate->published_at)->format('Y-m-d\TH:i')) }}" />
					</div>
					<div>
						<label for="scheduled_for" class="admin-field-label">Scheduled for</label>
						<input id="scheduled_for" type="datetime-local" name="scheduled_for" class="admin-field-input" value="{{ old('scheduled_for', optional($marketUpdate->scheduled_for)->format('Y-m-d\TH:i')) }}" />
					</div>
				</div>
				<div class="flex flex-wrap gap-6">
					<x-admin.toggle-switch name="featured" label="Featured" :checked="(bool) old('featured', $marketUpdate->featured)" />
					<x-admin.toggle-switch name="review_required" label="Review required" :checked="(bool) old('review_required', $marketUpdate->review_required)" />
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<x-admin.select name="author_type" label="Author type">
						<option value="{{ \App\Models\MarketUpdate::AUTHOR_TYPE_AI }}" @selected(old('author_type', $marketUpdate->author_type ?: \App\Models\MarketUpdate::AUTHOR_TYPE_AI) === \App\Models\MarketUpdate::AUTHOR_TYPE_AI)>AI</option>
						<option value="{{ \App\Models\MarketUpdate::AUTHOR_TYPE_HUMAN }}" @selected(old('author_type', $marketUpdate->author_type) === \App\Models\MarketUpdate::AUTHOR_TYPE_HUMAN)>Human</option>
					</x-admin.select>
					<x-admin.input name="author_name" label="Author name" value="{{ old('author_name', $marketUpdate->author_name ?: 'Ali 1.0') }}" />
				</div>
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Media</h3>
				<div>
					<label class="admin-field-label">Main image</label>
					<input type="file" name="main_image" accept="image/*" class="mt-1 block w-full text-sm text-zinc-600" />
					@if ($marketUpdate->main_image_path)
						<p class="mt-2 text-xs text-zinc-500">Stored path: {{ $marketUpdate->main_image_path }}</p>
					@endif
					@if ($marketUpdate->exists)
						<label class="mt-2 inline-flex items-center gap-2 text-xs text-zinc-600">
							<input type="checkbox" name="clear_main_image" value="1" class="size-4 rounded border-zinc-300" @checked(old('clear_main_image')) />
							Clear main image
						</label>
					@endif
				</div>
				<x-admin.input name="video_url" label="Video URL" value="{{ old('video_url', $marketUpdate->video_url) }}" />
				@if ($videoEmbed)
					<div class="aspect-video max-w-lg overflow-hidden rounded border border-zinc-200 bg-black">
						<iframe src="{{ $videoEmbed['embed_url'] }}" class="size-full border-0" title="Video preview"></iframe>
					</div>
				@endif
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">SEO</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $marketUpdate->meta_title) }}" />
					<x-admin.input name="meta_keywords" label="Meta keywords" value="{{ old('meta_keywords', $marketUpdate->meta_keywords) }}" />
				</div>
				<x-admin.textarea name="meta_description" label="Meta description" rows="3">{{ old('meta_description', $marketUpdate->meta_description) }}</x-admin.textarea>
				<x-admin.input name="canonical_url" label="Canonical URL" value="{{ old('canonical_url', $marketUpdate->canonical_url) }}" />
				<x-admin.input name="robots" label="Robots" value="{{ old('robots', $marketUpdate->robots ?: 'index,follow') }}" />
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Sources &amp; AI</h3>
				<x-admin.input name="source_topic" label="Source topic" value="{{ old('source_topic', $marketUpdate->source_topic) }}" />
				<x-admin.textarea name="fact_check_notes" label="Fact-check notes (admin)" rows="3">{{ old('fact_check_notes', $marketUpdate->fact_check_notes) }}</x-admin.textarea>
				<x-admin.textarea name="source_requirements" label="Source requirements" rows="3">{{ old('source_requirements', $marketUpdate->source_requirements) }}</x-admin.textarea>
				<x-admin.textarea name="source_urls_json" label="Source URLs JSON (future citations)" rows="4" class="font-mono text-xs">{{ $sourceUrlsOld }}</x-admin.textarea>
				<x-admin.textarea name="ai_prompt" label="AI prompt" rows="4" class="font-mono text-xs">{{ old('ai_prompt', $marketUpdate->ai_prompt) }}</x-admin.textarea>
			</div>

			@if ($categoryParents->isNotEmpty())
				<div class="space-y-2 border-t border-zinc-100 pt-6">
					<span class="admin-field-label">Categories &amp; subcategories</span>
					<div class="mt-2 space-y-4">
						@foreach ($categoryParents as $parent)
							<div>
								<label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-zinc-800">
									<input type="checkbox" name="category_term_ids[]" value="{{ $parent->id }}" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(in_array($parent->id, $selectedCategoryIds, true)) />
									{{ $parent->name }}
								</label>
								@php $kids = $categoryChildren->get((string) $parent->id, collect()); @endphp
								@if ($kids->isNotEmpty())
									<div class="ml-6 mt-2 flex flex-wrap gap-3">
										@foreach ($kids as $child)
											<label class="inline-flex cursor-pointer items-center gap-2 text-sm text-zinc-600">
												<input type="checkbox" name="category_term_ids[]" value="{{ $child->id }}" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(in_array($child->id, $selectedCategoryIds, true)) />
												{{ $child->name }}
											</label>
										@endforeach
									</div>
								@endif
							</div>
						@endforeach
					</div>
				</div>
			@endif

			@if ($tagTerms->isNotEmpty())
				<div class="space-y-2 border-t border-zinc-100 pt-6">
					<span class="admin-field-label">Tags</span>
					<div class="mt-2 flex flex-wrap gap-3">
						@foreach ($tagTerms as $term)
							<label class="inline-flex cursor-pointer items-center gap-2 text-sm text-zinc-700">
								<input type="checkbox" name="tag_term_ids[]" value="{{ $term->id }}" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(in_array($term->id, $selectedTagIds, true)) />
								{{ $term->name }}
							</label>
						@endforeach
					</div>
				</div>
			@endif

			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.market-updates.index')">Cancel</x-admin.button>
			</div>
		</form>
	</x-admin.card>
@endsection
@push('scripts')
	<script>
		$('#resource-form').on('submit', function (e) {
			e.preventDefault();
			var formData = new FormData(this);
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
				success: function (res) {
					adminToast(res.message || 'Saved.', 'success');
					setTimeout(function () {
						location = '{{ route('admin.market-updates.index') }}';
					}, 500);
				},
				error: function (xhr) {
					var m = 'Error';
					if (xhr.responseJSON && xhr.responseJSON.errors)
						m = Object.values(xhr.responseJSON.errors).flat().join(' ');
					adminToast(m, 'error');
				},
			});
		});
	</script>
@endpush
