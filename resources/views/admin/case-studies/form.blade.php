@extends('layouts.admin')
@section('title', $mode === 'create' ? 'New case study' : 'Edit case study')

@php
	$categoryTerms = $caseStudy->exists
		? $caseStudy->terms->filter(fn ($t) => ($t->taxonomy?->slug ?? '') === 'categories')
		: collect();
	$selectedCategoryIds = old('category_term_ids', $categoryTerms->pluck('id')->all());
	$selectedTagIds = old('tag_term_ids', $caseStudy->exists ? $caseStudy->terms->filter(fn ($t) => ($t->taxonomy?->slug ?? '') === 'tags')->pluck('id')->all() : []);
	$videoEmbed = $caseStudy->exists ? $caseStudy->video_embed : null;
	$techLines = old('technology_stack_text', $caseStudy->exists && is_array($caseStudy->technology_stack) ? implode("\n", $caseStudy->technology_stack) : '');
	$outcomeLines = old('outcomes_text', $caseStudy->exists && is_array($caseStudy->outcomes) ? implode("\n", $caseStudy->outcomes) : '');
	$galleryLines = old('gallery_urls_text', $caseStudy->exists && is_array($caseStudy->gallery_paths) ? implode("\n", $caseStudy->gallery_paths) : '');
	$metricsOld = old('metrics_json', $caseStudy->exists && is_array($caseStudy->metrics) ? json_encode($caseStudy->metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '');
@endphp

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New case study' : 'Edit case study'">
		<x-slot:actions>
			@if ($caseStudy->exists)
				<x-admin.button variant="ghost" :href="route('admin.case-studies.preview', $caseStudy)" target="_blank">Preview</x-admin.button>
			@endif
			<x-admin.button variant="ghost" :href="route('admin.case-studies.index')">Back</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.case-studies.store') : route('admin.case-studies.update', $caseStudy) }}"
			id="resource-form"
			class="space-y-8"
			enctype="multipart/form-data"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif

			<div class="space-y-4">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Basics</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="title" label="Title" value="{{ old('title', $caseStudy->title) }}" required />
					<x-admin.input name="slug" label="Slug" value="{{ old('slug', $caseStudy->slug) }}" />
				</div>
				@if ($caseStudy->slug_locked)
					<label class="inline-flex items-center gap-2 text-sm text-zinc-700">
						<input type="checkbox" name="unlock_slug" value="1" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(old('unlock_slug')) />
						Allow slug edit (unlock)
					</label>
				@endif
				<x-admin.select name="case_study_type" label="Case study type">
					<option value="{{ \App\Models\CaseStudy::TYPE_CONCEPT }}" @selected(old('case_study_type', $caseStudy->case_study_type ?: \App\Models\CaseStudy::TYPE_CONCEPT) === \App\Models\CaseStudy::TYPE_CONCEPT)>Concept / example</option>
					<option value="{{ \App\Models\CaseStudy::TYPE_ANONYMIZED }}" @selected(old('case_study_type', $caseStudy->case_study_type) === \App\Models\CaseStudy::TYPE_ANONYMIZED)>Anonymized</option>
					<option value="{{ \App\Models\CaseStudy::TYPE_REAL }}" @selected(old('case_study_type', $caseStudy->case_study_type) === \App\Models\CaseStudy::TYPE_REAL)>Real client</option>
				</x-admin.select>
				<x-admin.toggle-switch name="client_verified" label="Verified Artixcore delivery (real projects only)" :checked="(bool) old('client_verified', $caseStudy->client_verified)" />
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="client_name" label="Client name (internal)" value="{{ old('client_name', $caseStudy->client_name) }}" />
					<x-admin.input name="client_display_name" label="Display name / label" value="{{ old('client_display_name', $caseStudy->client_display_name) }}" />
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="industry" label="Industry" value="{{ old('industry', $caseStudy->industry) }}" />
					<x-admin.input name="project_type" label="Project type" value="{{ old('project_type', $caseStudy->project_type) }}" />
				</div>
				<x-admin.input name="summary" label="Excerpt" value="{{ old('summary', $caseStudy->summary) }}" />
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Narrative sections (HTML)</h3>
				<x-admin.textarea name="challenge" label="Challenge" rows="6" class="font-mono text-xs">{{ old('challenge', $caseStudy->challenge) }}</x-admin.textarea>
				<x-admin.textarea name="solution" label="Solution" rows="6" class="font-mono text-xs">{{ old('solution', $caseStudy->solution) }}</x-admin.textarea>
				<x-admin.textarea name="implementation" label="Implementation / architecture" rows="6" class="font-mono text-xs">{{ old('implementation', $caseStudy->implementation) }}</x-admin.textarea>
				<x-admin.textarea name="lessons_learned" label="Lessons learned" rows="5" class="font-mono text-xs">{{ old('lessons_learned', $caseStudy->lessons_learned) }}</x-admin.textarea>
				<x-admin.textarea name="body" label="Combined body (optional)" rows="8" class="font-mono text-xs">{{ old('body', $caseStudy->body) }}</x-admin.textarea>
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Structured fields</h3>
				<x-admin.textarea name="technology_stack_text" label="Technology stack (one per line)" rows="5" class="font-mono text-xs">{{ $techLines }}</x-admin.textarea>
				<x-admin.textarea name="outcomes_text" label="Outcomes (one bullet per line)" rows="5" class="font-mono text-xs">{{ $outcomeLines }}</x-admin.textarea>
				<x-admin.textarea name="metrics_json" label='Metrics JSON array of {label, note}' rows="5" class="font-mono text-xs">{{ $metricsOld }}</x-admin.textarea>
				<x-admin.textarea name="gallery_urls_text" label="Gallery image URLs (one per line)" rows="4" class="font-mono text-xs">{{ $galleryLines }}</x-admin.textarea>
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Publishing</h3>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
					<x-admin.select name="status" label="Status">
						@foreach ([
							\App\Models\CaseStudy::STATUS_DRAFT => 'Draft',
							\App\Models\CaseStudy::STATUS_PENDING_REVIEW => 'Pending review',
							\App\Models\CaseStudy::STATUS_SCHEDULED => 'Scheduled',
							\App\Models\CaseStudy::STATUS_PUBLISHED => 'Published',
							\App\Models\CaseStudy::STATUS_ARCHIVED => 'Archived',
						] as $val => $label)
							<option value="{{ $val }}" @selected(old('status', $caseStudy->status ?: \App\Models\CaseStudy::STATUS_DRAFT) === $val)>{{ $label }}</option>
						@endforeach
					</x-admin.select>
					<div>
						<label for="published_at" class="admin-field-label">Published at</label>
						<input id="published_at" type="datetime-local" name="published_at" class="admin-field-input" value="{{ old('published_at', optional($caseStudy->published_at)->format('Y-m-d\TH:i')) }}" />
					</div>
					<div>
						<label for="scheduled_for" class="admin-field-label">Scheduled for</label>
						<input id="scheduled_for" type="datetime-local" name="scheduled_for" class="admin-field-input" value="{{ old('scheduled_for', optional($caseStudy->scheduled_for)->format('Y-m-d\TH:i')) }}" />
					</div>
				</div>
				<div class="flex flex-wrap gap-6">
					<x-admin.toggle-switch name="featured" label="Featured" :checked="(bool) old('featured', $caseStudy->featured)" />
					<x-admin.toggle-switch name="review_required" label="Review required" :checked="(bool) old('review_required', $caseStudy->review_required)" />
				</div>
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
					<x-admin.select name="author_type" label="Author type">
						<option value="{{ \App\Models\CaseStudy::AUTHOR_TYPE_AI }}" @selected(old('author_type', $caseStudy->author_type ?: \App\Models\CaseStudy::AUTHOR_TYPE_AI) === \App\Models\CaseStudy::AUTHOR_TYPE_AI)>AI</option>
						<option value="{{ \App\Models\CaseStudy::AUTHOR_TYPE_HUMAN }}" @selected(old('author_type', $caseStudy->author_type) === \App\Models\CaseStudy::AUTHOR_TYPE_HUMAN)>Human</option>
					</x-admin.select>
					<x-admin.input name="author_name" label="Author name" value="{{ old('author_name', $caseStudy->author_name ?: 'Ali 1.0') }}" />
				</div>
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Media</h3>
				<div>
					<label class="admin-field-label">Main image upload</label>
					<input type="file" name="main_image" accept="image/*" class="mt-1 block w-full text-sm text-zinc-600" />
					@if ($caseStudy->main_image_path)
						<p class="mt-2 text-xs text-zinc-500">Stored path: {{ $caseStudy->main_image_path }}</p>
					@endif
					@if ($caseStudy->exists)
						<label class="mt-2 inline-flex items-center gap-2 text-xs text-zinc-600">
							<input type="checkbox" name="clear_main_image" value="1" class="size-4 rounded border-zinc-300" @checked(old('clear_main_image')) />
							Clear main image
						</label>
					@endif
				</div>
				<x-admin.input name="video_url" label="Video URL" value="{{ old('video_url', $caseStudy->video_url) }}" />
				@if ($videoEmbed)
					<div class="aspect-video max-w-lg overflow-hidden rounded border border-zinc-200 bg-black">
						<iframe src="{{ $videoEmbed['embed_url'] }}" class="size-full border-0" title="Video preview"></iframe>
					</div>
				@endif
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">SEO</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $caseStudy->meta_title) }}" />
					<x-admin.input name="meta_keywords" label="Meta keywords" value="{{ old('meta_keywords', $caseStudy->meta_keywords) }}" />
				</div>
				<x-admin.textarea name="meta_description" label="Meta description" rows="3">{{ old('meta_description', $caseStudy->meta_description) }}</x-admin.textarea>
				<x-admin.input name="canonical_url" label="Canonical URL" value="{{ old('canonical_url', $caseStudy->canonical_url) }}" />
				<x-admin.input name="robots" label="Robots" value="{{ old('robots', $caseStudy->robots ?: 'index,follow') }}" />
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Editorial / AI</h3>
				<x-admin.input name="source_topic" label="Source topic" value="{{ old('source_topic', $caseStudy->source_topic) }}" />
				<x-admin.textarea name="originality_notes" label="Originality notes" rows="3">{{ old('originality_notes', $caseStudy->originality_notes) }}</x-admin.textarea>
				<x-admin.textarea name="fact_check_notes" label="Fact-check notes" rows="3">{{ old('fact_check_notes', $caseStudy->fact_check_notes) }}</x-admin.textarea>
				<x-admin.textarea name="ai_prompt" label="AI prompt (optional)" rows="4" class="font-mono text-xs">{{ old('ai_prompt', $caseStudy->ai_prompt) }}</x-admin.textarea>
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
					<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Related content</h3>
					<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
						<div>
							<label class="admin-field-label">Related articles</label>
							<select name="related_article_ids[]" multiple class="admin-field-input h-40 font-mono text-xs">
								@foreach ($pickArticles as $row)
									<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedArticleIds ?? [], true))>{{ $row->title }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="admin-field-label">FAQs</label>
							<select name="faq_ids[]" multiple class="admin-field-input h-40 font-mono text-xs">
								@foreach ($pickFaqs as $row)
									<option value="{{ $row->id }}" @selected(in_array($row->id, $faqIds ?? [], true))>{{ \Illuminate\Support\Str::limit($row->question, 72) }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="admin-field-label">Testimonials</label>
							<select name="testimonial_ids[]" multiple class="admin-field-input h-40 font-mono text-xs">
								@foreach ($pickTestimonials as $row)
									<option value="{{ $row->id }}" @selected(in_array($row->id, $testimonialIds ?? [], true))>{{ $row->author_name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			@endisset

			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.case-studies.index')">Cancel</x-admin.button>
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
						location = '{{ route('admin.case-studies.index') }}';
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
