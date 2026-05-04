@extends('layouts.admin')

@section('title', 'Homepage')

@section('content')
	<x-admin.page-header title="Homepage">
		<x-slot:subtitle>Curate sections, featured content, and SEO. Changes apply to the public <code class="rounded bg-zinc-100 px-1 text-xs">/</code> route after save.</x-slot:subtitle>
		<x-slot:actions>
			<a
				href="{{ route('home') }}"
				target="_blank"
				rel="noopener"
				class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-800 shadow-sm transition hover:border-zinc-300"
			>Preview site</a>
		</x-slot:actions>
	</x-admin.page-header>

	<div class="mb-6 rounded-[10px] border border-amber-200/80 bg-amber-50 px-3 py-2 text-sm text-amber-950">
		<strong>Legacy note:</strong> the JSON field under
		<a href="{{ route('admin.marketing-content.edit') }}" class="font-medium underline">Marketing content</a>
		only applies when no homepage section rows exist. With sections seeded, this admin screen is the source of truth.
	</div>

	<x-admin.card class="mb-6">
		<h2 class="mb-4 text-base font-semibold text-zinc-900">Homepage SEO</h2>
		<form
			method="post"
			action="{{ route('admin.homepage.seo.update') }}"
			class="grid gap-4 sm:grid-cols-2"
			data-admin-ajax-form
		>
			@csrf
			@method('PATCH')
			<div class="sm:col-span-2">
				<x-admin.input name="meta_title" type="text" label="Meta title" value="{{ old('meta_title', $mergedSeo['meta_title'] ?? '') }}" data-error-for="meta_title" />
				<p class="mt-1 text-xs text-red-600" data-error-for="meta_title"></p>
			</div>
			<div class="sm:col-span-2">
				<x-admin.textarea name="meta_description" label="Meta description" rows="2" data-error-for="meta_description">{{ old('meta_description', $mergedSeo['meta_description'] ?? '') }}</x-admin.textarea>
				<p class="mt-1 text-xs text-red-600" data-error-for="meta_description"></p>
			</div>
			<div class="sm:col-span-2">
				<x-admin.textarea name="meta_keywords" label="Meta keywords" rows="2" data-error-for="meta_keywords">{{ old('meta_keywords', $mergedSeo['meta_keywords'] ?? '') }}</x-admin.textarea>
				<p class="mt-1 text-xs text-red-600" data-error-for="meta_keywords"></p>
			</div>
			<x-admin.input name="canonical_url" label="Canonical URL" value="{{ old('canonical_url', $mergedSeo['canonical_url'] ?? '') }}" data-error-for="canonical_url" />
			<x-admin.input name="robots" label="Robots" value="{{ old('robots', $mergedSeo['robots'] ?? '') }}" data-error-for="robots" />
			<x-admin.input name="og_title" label="OG title" value="{{ old('og_title', $mergedSeo['og_title'] ?? '') }}" data-error-for="og_title" />
			<x-admin.input name="og_description" label="OG description" value="{{ old('og_description', $mergedSeo['og_description'] ?? '') }}" data-error-for="og_description" />
			<x-admin.input name="og_image" label="OG image URL" value="{{ old('og_image', $mergedSeo['og_image'] ?? '') }}" data-error-for="og_image" />
			<x-admin.input name="twitter_title" label="Twitter title" value="{{ old('twitter_title', $mergedSeo['twitter_title'] ?? '') }}" data-error-for="twitter_title" />
			<x-admin.input name="twitter_description" label="Twitter description" value="{{ old('twitter_description', $mergedSeo['twitter_description'] ?? '') }}" data-error-for="twitter_description" />
			<x-admin.input name="twitter_image" label="Twitter image URL" value="{{ old('twitter_image', $mergedSeo['twitter_image'] ?? '') }}" data-error-for="twitter_image" />
			<div class="sm:col-span-2">
				<x-admin.button variant="primary" type="submit">Save SEO</x-admin.button>
			</div>
		</form>
	</x-admin.card>

	@foreach($sections as $section)
		@php
			$settingsJson = json_encode($section->settings ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		@endphp
		<x-admin.card class="mb-6" data-admin-row="section-{{ $section->id }}">
			<div class="mb-3 flex flex-wrap items-center justify-between gap-2">
				<div>
					<h2 class="text-base font-semibold text-zinc-900">{{ $section->key }}</h2>
					<p class="text-xs text-zinc-500">Blade: <code>home.sections.{{ \App\Services\HomepageContentResolver::SECTION_PARTIALS[$section->key] ?? '—' }}</code></p>
				</div>
				<span class="rounded bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600">#{{ $section->id }}</span>
			</div>

			<form
				method="post"
				action="{{ route('admin.homepage.sections.update', $section) }}"
				class="mb-4 grid gap-3 sm:grid-cols-2"
				data-admin-ajax-form
			>
				@csrf
				@method('PATCH')
				<input type="hidden" name="key" value="{{ $section->key }}">
				<div>
					<label class="mb-1.5 block text-sm font-medium text-zinc-700">Enabled</label>
					<input type="hidden" name="is_enabled" value="0">
					<input type="checkbox" name="is_enabled" value="1" class="h-4 w-4 rounded border-zinc-300" @checked($section->is_enabled)>
				</div>
				<x-admin.input name="sort_order" type="number" label="Sort order" :value="(string) $section->sort_order" data-error-for="sort_order" />
				<p class="text-xs text-red-600 sm:col-span-2" data-error-for="sort_order"></p>
				<div class="sm:col-span-2">
					<x-admin.input name="title" label="Title" :value="$section->title" data-error-for="title" />
					<p class="mt-1 text-xs text-red-600" data-error-for="title"></p>
				</div>
				<div class="sm:col-span-2">
					<x-admin.input name="subtitle" label="Subtitle" :value="$section->subtitle" data-error-for="subtitle" />
					<p class="mt-1 text-xs text-red-600" data-error-for="subtitle"></p>
				</div>
				<div class="sm:col-span-2">
					<x-admin.textarea name="description" label="Description" rows="3" data-error-for="description">{{ $section->description }}</x-admin.textarea>
					<p class="mt-1 text-xs text-red-600" data-error-for="description"></p>
				</div>
				<x-admin.input name="badge_text" label="Badge" :value="$section->badge_text" data-error-for="badge_text" />
				<x-admin.input name="image_path" label="Image path (URL or storage path)" :value="$section->image_path" data-error-for="image_path" />
				<x-admin.input name="button_text" label="Primary button" :value="$section->button_text" data-error-for="button_text" />
				<x-admin.input name="button_url" label="Primary URL" :value="$section->button_url" data-error-for="button_url" />
				<x-admin.input name="secondary_button_text" label="Secondary button" :value="$section->secondary_button_text" data-error-for="secondary_button_text" />
				<x-admin.input name="secondary_button_url" label="Secondary URL" :value="$section->secondary_button_url" data-error-for="secondary_button_url" />
				<div class="sm:col-span-2">
					<x-admin.textarea name="settings_json" label="Settings (JSON)" rows="6" class="font-mono text-xs" data-error-for="settings_json">{{ $settingsJson }}</x-admin.textarea>
					<p class="mt-1 text-xs text-red-600" data-error-for="settings_json"></p>
				</div>
				<div class="sm:col-span-2">
					<x-admin.button variant="primary" type="submit">Save section</x-admin.button>
				</div>
			</form>

			<form
				method="post"
				action="{{ route('admin.homepage.sections.image', $section) }}"
				enctype="multipart/form-data"
				class="mb-4 flex flex-wrap items-end gap-3 border-t border-zinc-100 pt-4"
				data-admin-ajax-form
			>
				@csrf
				<div>
					<label class="mb-1.5 block text-sm font-medium text-zinc-700">Upload hero / section image</label>
					<input type="file" name="image" accept="image/*" class="text-sm" required>
				</div>
				<x-admin.button variant="secondary" type="submit">Upload</x-admin.button>
			</form>

			<div class="border-t border-zinc-100 pt-4">
				<h3 class="mb-2 text-sm font-semibold text-zinc-800">Linked items</h3>
				<div class="overflow-x-auto">
					<table class="min-w-full text-left text-sm">
						<thead>
							<tr class="border-b border-zinc-200 text-xs uppercase text-zinc-500">
								<th class="py-2 pr-3">Type</th>
								<th class="py-2 pr-3">ID</th>
								<th class="py-2 pr-3">Sort</th>
								<th class="py-2 pr-3">On</th>
								<th class="py-2"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($section->items as $item)
								<tr data-admin-row class="border-b border-zinc-100">
									<td class="py-2 pr-3">{{ $itemTypeLabels[$item->item_type] ?? $item->item_type }}</td>
									<td class="py-2 pr-3">{{ $item->item_id }}</td>
									<td class="py-2 pr-3">{{ $item->sort_order }}</td>
									<td class="py-2 pr-3">{{ $item->is_enabled ? 'Yes' : 'No' }}</td>
									<td class="py-2 text-end">
										<button
											type="button"
											class="text-xs text-red-600 hover:underline"
											data-admin-delete="{{ route('admin.homepage.items.destroy', $item) }}"
										>Remove</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<form method="post" action="{{ route('admin.homepage.items.store', $section) }}" class="mt-4 flex flex-wrap items-end gap-2" data-admin-ajax-form>
					@csrf
					<div>
						<label class="mb-1 block text-xs font-medium text-zinc-600">Attach content</label>
						<select name="pick" class="block min-w-[280px] rounded-[10px] border border-zinc-200 bg-white px-2 py-2 text-sm" required data-error-for="pick">
							<option value="">Select…</option>
							@foreach($itemTypeLabels as $type => $label)
								<optgroup label="{{ $label }}">
									@foreach($picklists[$type] ?? [] as $opt)
										<option value="{{ $type }}:{{ $opt['id'] }}">{{ $opt['label'] }}</option>
									@endforeach
								</optgroup>
							@endforeach
						</select>
						<p class="mt-1 text-xs text-red-600" data-error-for="pick"></p>
						<p class="mt-1 text-xs text-red-600" data-error-for="item_id"></p>
					</div>
					<x-admin.button variant="primary" type="submit">Attach</x-admin.button>
				</form>
			</div>
		</x-admin.card>
	@endforeach

	@if($sections->isEmpty())
		<x-admin.card>
			<p class="text-sm text-zinc-600">No homepage sections yet. Run <code class="rounded bg-zinc-100 px-1">php artisan db:seed --class=HomepageSeeder --force</code>.</p>
		</x-admin.card>
	@endif
@endsection
