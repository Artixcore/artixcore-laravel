@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New article' : 'Edit article')

@php
	$categoryTerms = $article->exists
		? $article->terms->filter(fn ($t) => ($t->taxonomy?->slug ?? '') === 'categories')
		: collect();
	$selectedCategoryIds = old('category_term_ids', $categoryTerms->pluck('id')->all());
	$selectedTagIds = old('tag_term_ids', $article->exists ? $article->terms->filter(fn ($t) => ($t->taxonomy?->slug ?? '') === 'tags')->pluck('id')->all() : []);
	$videoEmbed = $article->exists ? $article->video_embed : null;
@endphp

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New article' : 'Edit article'">
		<x-slot:actions>
			@if ($article->exists)
				<x-admin.button variant="ghost" :href="route('admin.articles.preview', $article)" target="_blank">Preview</x-admin.button>
			@endif
			<x-admin.button variant="ghost" :href="route('admin.articles.index')">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.articles.store') : route('admin.articles.update', $article) }}"
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
					<x-admin.input name="title" label="Title" value="{{ old('title', $article->title) }}" required />
					<x-admin.input name="slug" label="Slug" value="{{ old('slug', $article->slug) }}" hint="Locked after publish unless you unlock below." />
				</div>
				@if ($article->slug_locked)
					<label class="inline-flex items-center gap-2 text-sm text-zinc-700">
						<input type="checkbox" name="unlock_slug" value="1" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(old('unlock_slug')) />
						Allow slug edit (unlock)
					</label>
				@endif
				<x-admin.input name="summary" label="Excerpt / summary" value="{{ old('summary', $article->summary) }}" />
				<x-admin.textarea name="body" label="Body (HTML)" rows="14" class="font-mono text-xs">{{ old('body', $article->body) }}</x-admin.textarea>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<x-admin.select name="article_type" label="Article type">
						<option value="">—</option>
						@foreach ([
							'service_article',
							'platform_article',
							'portfolio_article',
							'case_study_support',
							'technology_article',
							'market_update',
							'tutorial',
							'insight',
							'latest_discovery',
							'today_trends',
							'latest_tech',
							'company_update',
						] as $t)
							<option value="{{ $t }}" @selected(old('article_type', $article->article_type) === $t)>{{ $t }}</option>
						@endforeach
					</x-admin.select>
					<x-admin.select name="author_type" label="Author type">
						<option value="{{ \App\Models\Article::AUTHOR_TYPE_AI }}" @selected(old('author_type', $article->author_type ?: \App\Models\Article::AUTHOR_TYPE_AI) === \App\Models\Article::AUTHOR_TYPE_AI)>AI</option>
						<option value="{{ \App\Models\Article::AUTHOR_TYPE_HUMAN }}" @selected(old('author_type', $article->author_type) === \App\Models\Article::AUTHOR_TYPE_HUMAN)>Human</option>
					</x-admin.select>
				</div>
				<x-admin.input name="author_name" label="Author name" value="{{ old('author_name', $article->author_name ?: 'Ali 1.0') }}" />
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Publishing</h3>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
					<x-admin.select name="status" label="Status">
						@foreach ([
							\App\Models\Article::STATUS_DRAFT => 'Draft',
							\App\Models\Article::STATUS_PENDING_REVIEW => 'Pending review',
							\App\Models\Article::STATUS_SCHEDULED => 'Scheduled',
							\App\Models\Article::STATUS_PUBLISHED => 'Published',
							\App\Models\Article::STATUS_ARCHIVED => 'Archived',
						] as $val => $label)
							<option value="{{ $val }}" @selected(old('status', $article->status) === $val)>{{ $label }}</option>
						@endforeach
					</x-admin.select>
					<div>
						<label for="published_at" class="admin-field-label">Published at</label>
						<input id="published_at" type="datetime-local" name="published_at" class="admin-field-input" value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}" />
					</div>
					<div>
						<label for="scheduled_for" class="admin-field-label">Scheduled for</label>
						<input id="scheduled_for" type="datetime-local" name="scheduled_for" class="admin-field-input" value="{{ old('scheduled_for', optional($article->scheduled_for)->format('Y-m-d\TH:i')) }}" />
					</div>
				</div>
				<div class="flex flex-wrap gap-6">
					<x-admin.toggle-switch name="featured" label="Featured" :checked="(bool) old('featured', $article->featured)" />
					<x-admin.toggle-switch name="review_required" label="Review required" :checked="(bool) old('review_required', $article->review_required)" />
				</div>
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Media</h3>
				<div>
					<label class="admin-field-label">Main image</label>
					<input type="file" name="main_image" accept="image/*" class="mt-1 block w-full text-sm text-zinc-600" />
					@if ($article->exists && $article->getFirstMediaUrl('article_main'))
						<p class="mt-2 text-xs text-zinc-500">Current: <a href="{{ $article->getFirstMediaUrl('article_main') }}" target="_blank" class="text-indigo-600">view</a></p>
					@endif
				</div>
				<div>
					<label class="admin-field-label">Gallery images (append)</label>
					<input type="file" name="gallery_images[]" accept="image/*" multiple class="mt-1 block w-full text-sm text-zinc-600" />
				</div>
				<x-admin.input name="video_url" label="Video URL (YouTube / Vimeo)" value="{{ old('video_url', $article->video_url) }}" />
				@if ($videoEmbed)
					<div class="aspect-video max-w-lg overflow-hidden rounded border border-zinc-200 bg-black">
						<iframe src="{{ $videoEmbed['embed_url'] }}" class="size-full border-0" title="Video preview"></iframe>
					</div>
				@endif
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">SEO</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $article->meta_title) }}" />
					<x-admin.input name="meta_keywords" label="Meta keywords" value="{{ old('meta_keywords', $article->meta_keywords) }}" />
				</div>
				<x-admin.textarea name="meta_description" label="Meta description" rows="3">{{ old('meta_description', $article->meta_description) }}</x-admin.textarea>
				<x-admin.input name="canonical_url" label="Canonical URL" value="{{ old('canonical_url', $article->canonical_url) }}" />
				<x-admin.input name="robots" label="Robots" value="{{ old('robots', $article->robots ?: 'index,follow') }}" />
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

			@isset($pickArticles)
				<div class="space-y-4 border-t border-zinc-100 pt-6">
					<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Related content (graph)</h3>
					<p class="text-xs text-zinc-600">Curated relationships for the public article page (optional).</p>
					<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
						<div>
							<label for="related_article_ids" class="admin-field-label">Related articles</label>
							<select id="related_article_ids" name="related_article_ids[]" multiple class="admin-field-input h-44 font-mono text-xs">
								@foreach ($pickArticles as $pa)
									@if ($article->exists && (int) $pa->id === (int) $article->id)
										@continue
									@endif
									<option value="{{ $pa->id }}" @selected(in_array($pa->id, $relatedArticleIds ?? [], true))>{{ $pa->title }}</option>
								@endforeach
							</select>
							<p class="mt-1 text-xs text-zinc-500">Hold Ctrl/Cmd to select multiple.</p>
						</div>
						<div>
							<label for="related_case_study_ids" class="admin-field-label">Related case studies</label>
							<select id="related_case_study_ids" name="related_case_study_ids[]" multiple class="admin-field-input h-44 font-mono text-xs">
								@foreach ($pickCaseStudies as $cs)
									<option value="{{ $cs->id }}" @selected(in_array($cs->id, $relatedCaseStudyIds ?? [], true))>{{ $cs->title }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			@endisset

			@if ($article->exists && ($article->ai_prompt || $article->ai_generation_meta))
				<div class="space-y-2 border-t border-zinc-100 pt-6">
					<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">AI metadata</h3>
					@if ($article->ai_model)
						<p class="text-xs text-zinc-600">Model: {{ $article->ai_model }}</p>
					@endif
					@if ($article->source_topic)
						<p class="text-xs text-zinc-600">Source topic: {{ $article->source_topic }}</p>
					@endif
					@if ($article->ai_prompt)
						<div>
							<label class="admin-field-label">Prompt (read-only)</label>
							<textarea rows="6" readonly class="admin-field-input font-mono text-xs bg-zinc-50">{{ $article->ai_prompt }}</textarea>
						</div>
					@endif
				</div>
			@endif

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Originality</h3>
				<x-admin.textarea name="originality_notes" label="Originality notes" rows="4">{{ old('originality_notes', $article->originality_notes) }}</x-admin.textarea>
				<x-admin.input name="plagiarism_score" label="Plagiarism score (manual)" type="number" step="0.01" min="0" max="100" value="{{ old('plagiarism_score', $article->plagiarism_score) }}" />
			</div>

			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.articles.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.articles.index') }}';
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
