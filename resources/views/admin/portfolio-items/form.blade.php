@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New portfolio item' : 'Edit portfolio item')

@php
	$techDefault = old('technology_stack_json', json_encode($item->technology_stack ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
@endphp

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New portfolio item' : 'Edit portfolio item'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.portfolio-items.store') : route('admin.portfolio-items.update', $item) }}"
			id="resource-form"
			class="space-y-6"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif

			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $item->title) }}" required />
				<x-admin.input name="slug" label="Slug" value="{{ old('slug', $item->slug) }}" />
			</div>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<x-admin.input name="client_name" label="Client name" value="{{ old('client_name', $item->client_name) }}" />
				<x-admin.input name="project_type" label="Project type" value="{{ old('project_type', $item->project_type) }}" />
				<x-admin.input name="industry" label="Industry" value="{{ old('industry', $item->industry) }}" />
			</div>
			<x-admin.input name="short_description" label="Short description" value="{{ old('short_description', $item->short_description) }}" />
			<x-admin.textarea name="body" label="Body (HTML)" rows="8" class="font-mono text-xs">{{ old('body', $item->body) }}</x-admin.textarea>
			<x-admin.textarea name="challenge" label="Challenge (HTML)" rows="5" class="font-mono text-xs">{{ old('challenge', $item->challenge) }}</x-admin.textarea>
			<x-admin.textarea name="solution" label="Solution (HTML)" rows="5" class="font-mono text-xs">{{ old('solution', $item->solution) }}</x-admin.textarea>
			<x-admin.textarea name="outcome" label="Outcome" rows="3">{{ old('outcome', $item->outcome) }}</x-admin.textarea>

			<div>
				<label class="admin-field-label">Technology stack (JSON array of strings)</label>
				<textarea name="technology_stack_json" rows="5" class="admin-field-input font-mono text-xs">{{ $techDefault }}</textarea>
			</div>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="main_image_media_id" label="Main image media ID" type="number" value="{{ old('main_image_media_id', $item->main_image_media_id) }}" />
				<x-admin.input name="video_url" label="Video URL (YouTube/Vimeo)" value="{{ old('video_url', $item->video_url) }}" />
			</div>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<x-admin.select name="status" label="Status">
					<option value="{{ \App\Models\PortfolioItem::STATUS_DRAFT }}" @selected(old('status', $item->status) === \App\Models\PortfolioItem::STATUS_DRAFT)>draft</option>
					<option value="{{ \App\Models\PortfolioItem::STATUS_PUBLISHED }}" @selected(old('status', $item->status) === \App\Models\PortfolioItem::STATUS_PUBLISHED)>published</option>
				</x-admin.select>
				<x-admin.input name="sort_order" label="Sort order" type="number" value="{{ old('sort_order', $item->sort_order ?? 0) }}" />
				<div>
					<label for="published_at" class="admin-field-label">Published at</label>
					<input id="published_at" type="datetime-local" name="published_at" class="admin-field-input" value="{{ old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')) }}" />
				</div>
			</div>

			<label class="inline-flex items-center gap-2 text-sm text-zinc-700">
				<input type="hidden" name="featured" value="0" />
				<input type="checkbox" name="featured" value="1" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(old('featured', $item->featured ?? false)) />
				Featured
			</label>

			<div class="space-y-2 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">SEO</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $item->meta_title) }}" />
					<x-admin.input name="meta_keywords" label="Meta keywords" value="{{ old('meta_keywords', $item->meta_keywords) }}" />
				</div>
				<x-admin.textarea name="meta_description" label="Meta description" rows="3">{{ old('meta_description', $item->meta_description) }}</x-admin.textarea>
				<x-admin.input name="canonical_url" label="Canonical URL" value="{{ old('canonical_url', $item->canonical_url) }}" />
				<x-admin.input name="robots" label="Robots" value="{{ old('robots', $item->robots ?: 'index,follow') }}" />
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Related content</h3>
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
					<div>
						<label class="admin-field-label">Services</label>
						<select name="related_service_ids[]" multiple class="admin-field-input h-32 font-mono text-xs">
							@foreach ($pickServices as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedServiceIds ?? [], true))>{{ $row->title }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label">Articles</label>
						<select name="related_article_ids[]" multiple class="admin-field-input h-32 font-mono text-xs">
							@foreach ($pickArticles as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedArticleIds ?? [], true))>{{ $row->title }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label">Case studies</label>
						<select name="related_case_study_ids[]" multiple class="admin-field-input h-32 font-mono text-xs">
							@foreach ($pickCaseStudies as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedCaseStudyIds ?? [], true))>{{ $row->title }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
					<div>
						<label class="admin-field-label">FAQs</label>
						<select name="faq_ids[]" multiple class="admin-field-input h-32 font-mono text-xs">
							@foreach ($pickFaqs as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $faqIds ?? [], true))>{{ \Illuminate\Support\Str::limit($row->question, 72) }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label">Testimonials</label>
						<select name="testimonial_ids[]" multiple class="admin-field-input h-32 font-mono text-xs">
							@foreach ($pickTestimonials as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $testimonialIds ?? [], true))>{{ $row->author_name }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.portfolio-items.index')">Cancel</x-admin.button>
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
						window.location = '{{ route('admin.portfolio-items.index') }}';
					}, 600);
				},
				error: function (xhr) {
					var m = 'Could not save';
					if (xhr.responseJSON && xhr.responseJSON.errors)
						m = Object.values(xhr.responseJSON.errors).flat().join(' ');
					adminToast(m, 'error');
				},
			});
		});
	</script>
@endpush
