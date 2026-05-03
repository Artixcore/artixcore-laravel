@extends('layouts.admin')

@section('title', $mode === 'create' ? 'New service' : 'Edit service')

@php
	$benefitsDefault = old('benefits_json', json_encode($service->benefits ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	$processDefault = old('process_json', json_encode($service->process ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	$techDefault = old('technologies_json', json_encode($service->technologies ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
@endphp

@section('content')
	<x-admin.page-header :title="$mode === 'create' ? 'New service' : 'Edit service'" />

	<x-admin.card>
		<form
			method="post"
			action="{{ $mode === 'create' ? route('admin.services.store') : route('admin.services.update', $service) }}"
			class="space-y-6"
			id="resource-form"
		>
			@csrf
			@if ($mode === 'edit')
				@method('PUT')
			@endif
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.input name="title" label="Title" value="{{ old('title', $service->title) }}" required />
				<x-admin.input name="slug" label="Slug (optional)" value="{{ old('slug', $service->slug) }}" />
			</div>
			<x-admin.input name="summary" label="Summary" value="{{ old('summary', $service->summary) }}" />
			<x-admin.textarea name="body" label="Body (HTML ok)" rows="8" class="font-mono text-xs">{{ old('body', $service->body) }}</x-admin.textarea>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<div>
					<label class="admin-field-label">Benefits (JSON array)</label>
					<textarea name="benefits_json" rows="6" class="admin-field-input font-mono text-xs">{{ $benefitsDefault }}</textarea>
					<p class="mt-1 text-xs text-zinc-500">Example: <code>["Faster delivery","Lower risk"]</code> or objects with title/body.</p>
				</div>
				<div>
					<label class="admin-field-label">Process (JSON array)</label>
					<textarea name="process_json" rows="6" class="admin-field-input font-mono text-xs">{{ $processDefault }}</textarea>
				</div>
				<div>
					<label class="admin-field-label">Technologies (JSON array)</label>
					<textarea name="technologies_json" rows="6" class="admin-field-input font-mono text-xs">{{ $techDefault }}</textarea>
				</div>
			</div>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<x-admin.input name="icon" label="Icon (Bootstrap Icons class)" value="{{ old('icon', $service->icon) }}" placeholder="bi bi-stack" />
				<x-admin.input
					name="featured_image_media_id"
					label="Featured image media ID"
					type="number"
					value="{{ old('featured_image_media_id', $service->featured_image_media_id) }}"
				/>
				<x-admin.input
					name="sort_order"
					label="Sort order"
					type="number"
					value="{{ old('sort_order', $service->sort_order ?? 0) }}"
				/>
			</div>

			<label class="inline-flex items-center gap-2 text-sm text-zinc-700">
				<input type="hidden" name="featured" value="0" />
				<input type="checkbox" name="featured" value="1" class="size-4 rounded border-zinc-300 text-indigo-600" @checked(old('featured', $service->featured ?? false)) />
				Featured on listings
			</label>

			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<x-admin.select name="status" label="Status">
					<option value="draft" @selected(old('status', $service->status) === 'draft')>draft</option>
					<option value="published" @selected(old('status', $service->status) === 'published')>published</option>
				</x-admin.select>
				<div>
					<label for="published_at" class="admin-field-label">Published at</label>
					<input
						id="published_at"
						type="datetime-local"
						name="published_at"
						class="admin-field-input"
						value="{{ old('published_at', optional($service->published_at)->format('Y-m-d\TH:i')) }}"
					/>
				</div>
			</div>

			<div class="space-y-2 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">SEO</h3>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<x-admin.input name="meta_title" label="Meta title" value="{{ old('meta_title', $service->meta_title) }}" />
					<x-admin.input name="meta_keywords" label="Meta keywords" value="{{ old('meta_keywords', $service->meta_keywords) }}" />
				</div>
				<x-admin.textarea name="meta_description" label="Meta description" rows="3">{{ old('meta_description', $service->meta_description) }}</x-admin.textarea>
				<x-admin.input name="canonical_url" label="Canonical URL" value="{{ old('canonical_url', $service->canonical_url) }}" />
				<x-admin.input name="robots" label="Robots" value="{{ old('robots', $service->robots ?: 'index,follow') }}" />
			</div>

			<div class="space-y-4 border-t border-zinc-100 pt-6">
				<h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500">Related content</h3>
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
					<div>
						<label class="admin-field-label">Articles</label>
						<select name="related_article_ids[]" multiple class="admin-field-input h-36 font-mono text-xs">
							@foreach ($pickArticles as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedArticleIds ?? [], true))>{{ $row->title }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label">Case studies</label>
						<select name="related_case_study_ids[]" multiple class="admin-field-input h-36 font-mono text-xs">
							@foreach ($pickCaseStudies as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedCaseStudyIds ?? [], true))>{{ $row->title }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label">Portfolio</label>
						<select name="related_portfolio_ids[]" multiple class="admin-field-input h-36 font-mono text-xs">
							@foreach ($pickPortfolio as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $relatedPortfolioIds ?? [], true))>{{ $row->title }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
					<div>
						<label class="admin-field-label">FAQs</label>
						<select name="faq_ids[]" multiple class="admin-field-input h-36 font-mono text-xs">
							@foreach ($pickFaqs as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $faqIds ?? [], true))>{{ \Illuminate\Support\Str::limit($row->question, 80) }}</option>
							@endforeach
						</select>
					</div>
					<div>
						<label class="admin-field-label">Testimonials</label>
						<select name="testimonial_ids[]" multiple class="admin-field-input h-36 font-mono text-xs">
							@foreach ($pickTestimonials as $row)
								<option value="{{ $row->id }}" @selected(in_array($row->id, $testimonialIds ?? [], true))>{{ $row->author_name }} @if($row->company) — {{ $row->company }} @endif</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
				<x-admin.button variant="primary" type="submit">Save</x-admin.button>
				<x-admin.button variant="ghost" :href="route('admin.services.index')">Cancel</x-admin.button>
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
						window.location = '{{ route('admin.services.index') }}';
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
