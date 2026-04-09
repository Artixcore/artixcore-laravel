@extends('layouts.admin')

@section('title', 'SEO settings')

@php
	$m = $seo['meta'] ?? [];
	$g = $seo['google'] ?? [];
	$t = $seo['twitter'] ?? [];
	$tt = $seo['tiktok'] ?? [];
	$a = $seo['additional'] ?? [];
@endphp

@section('content')
	<x-admin.page-header title="SEO settings">
		<x-slot:subtitle>
			Integrations and default social meta. Open Graph defaults fall back to
			<a href="{{ route('admin.site-settings.edit') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Site settings</a> when overrides are empty.
		</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card>
	<form method="post" action="{{ route('admin.seo-settings.update') }}" id="seo-settings-form" class="space-y-6">
	@csrf
	@method('PUT')
	@if($errors->any())
		<div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">
			<ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
		</div>
	@endif
	<div>
		<ul class="mb-4 flex flex-wrap gap-1 border-b border-zinc-200" id="seo-tabs" role="tablist">
			<li class="list-none" role="presentation">
				<button class="nav-link active rounded-t-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50" id="tab-meta" data-bs-toggle="tab" data-bs-target="#pane-meta" type="button" role="tab">Meta (Facebook)</button>
			</li>
			<li class="list-none" role="presentation">
				<button class="nav-link rounded-t-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50" id="tab-google" data-bs-toggle="tab" data-bs-target="#pane-google" type="button" role="tab">Google</button>
			</li>
			<li class="list-none" role="presentation">
				<button class="nav-link rounded-t-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50" id="tab-twitter" data-bs-toggle="tab" data-bs-target="#pane-twitter" type="button" role="tab">Twitter (X)</button>
			</li>
			<li class="list-none" role="presentation">
				<button class="nav-link rounded-t-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50" id="tab-tiktok" data-bs-toggle="tab" data-bs-target="#pane-tiktok" type="button" role="tab">TikTok</button>
			</li>
			<li class="list-none" role="presentation">
				<button class="nav-link rounded-t-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50" id="tab-additional" data-bs-toggle="tab" data-bs-target="#pane-additional" type="button" role="tab">Additional</button>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade show active" id="pane-meta" role="tabpanel">
				<div class="mb-4">
					<div class="flex items-center gap-3 py-1">
						<input type="hidden" name="seo[meta][enabled]" value="0">
						<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[meta][enabled]" id="meta-enabled" value="1" @checked(old('seo.meta.enabled', $m['enabled'] ?? false))>
						<label class="text-sm font-semibold text-zinc-900" for="meta-enabled">Enable Meta integration</label>
					</div>
					<p class="admin-field-hint">Master switch for Meta Pixel, App ID, and OG overrides below.</p>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="meta-pixel">Meta Pixel ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[meta][pixel_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[meta][pixel_id_active]" id="meta-pixel-active" value="1" @checked(old('seo.meta.pixel_id_active', $m['pixel_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="meta-pixel-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input" id="meta-pixel" name="seo[meta][pixel_id]" value="{{ old('seo.meta.pixel_id', $m['pixel_id'] ?? '') }}" inputmode="numeric" autocomplete="off">
						<div class="admin-field-hint">Numeric ID from Meta Events Manager. <span class="text-zinc-500" data-bs-toggle="tooltip" data-bs-title="Loaded only when integration is enabled and this field is on.">?</span></div>
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="meta-app">App ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[meta][app_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[meta][app_id_active]" id="meta-app-active" value="1" @checked(old('seo.meta.app_id_active', $m['app_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="meta-app-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input" id="meta-app" name="seo[meta][app_id]" value="{{ old('seo.meta.app_id', $m['app_id'] ?? '') }}" autocomplete="off">
						<div class="admin-field-hint">Used for <code>fb:app_id</code> Open Graph meta. <span class="text-zinc-500" data-bs-toggle="tooltip" data-bs-title="Optional; enables Facebook app attribution in shared links.">?</span></div>
					</div>
					<div class="md:col-span-2 min-w-0"><hr class="border-zinc-200"></div>
					<div class="md:col-span-2 min-w-0">
						<h2 class="text-sm font-semibold text-zinc-500">Open Graph overrides</h2>
						<p class="text-sm text-zinc-500">Leave blank to use Site settings defaults.</p>
					</div>
					<div class="min-w-0">
						<label class="admin-field-label" for="meta-og-title">Default OG title</label>
						<input type="text" class="admin-field-input" id="meta-og-title" name="seo[meta][og_title_override]" value="{{ old('seo.meta.og_title_override', $m['og_title_override'] ?? '') }}" maxlength="255">
					</div>
					<div class="min-w-0">
						<label class="admin-field-label" for="meta-og-img">Default OG image URL</label>
						<input type="url" class="admin-field-input" id="meta-og-img" name="seo[meta][og_image_url]" value="{{ old('seo.meta.og_image_url', $m['og_image_url'] ?? '') }}" placeholder="https://…">
						<div class="admin-field-hint">Full HTTPS URL. Overrides uploaded OG image in Site settings when set.</div>
					</div>
					<div class="md:col-span-2 min-w-0">
						<label class="admin-field-label" for="meta-og-desc">Default OG description</label>
						<textarea class="admin-field-input" id="meta-og-desc" name="seo[meta][og_description_override]" rows="3" maxlength="2000">{{ old('seo.meta.og_description_override', $m['og_description_override'] ?? '') }}</textarea>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-google" role="tabpanel">
				<div class="flex items-center gap-3 py-1 mb-4">
					<input type="hidden" name="seo[google][enabled]" value="0">
					<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[google][enabled]" id="google-enabled" value="1" @checked(old('seo.google.enabled', $g['enabled'] ?? false))>
					<label class="text-sm font-semibold text-zinc-900" for="google-enabled">Enable Google integration</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="ga4">GA4 measurement ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[google][ga4_measurement_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[google][ga4_measurement_id_active]" id="ga4-active" value="1" @checked(old('seo.google.ga4_measurement_id_active', $g['ga4_measurement_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="ga4-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input font-mono text-xs" id="ga4" name="seo[google][ga4_measurement_id]" value="{{ old('seo.google.ga4_measurement_id', $g['ga4_measurement_id'] ?? '') }}" placeholder="G-XXXXXXXXXX" autocomplete="off">
						<div class="admin-field-hint">Format <code>G-…</code>. Loads gtag when enabled. <span class="text-zinc-500" data-bs-toggle="tooltip" data-bs-title="Avoid duplicating the same property in GTM if you use both.">?</span></div>
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="gtm">Google Tag Manager ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[google][gtm_container_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[google][gtm_container_id_active]" id="gtm-active" value="1" @checked(old('seo.google.gtm_container_id_active', $g['gtm_container_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="gtm-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input font-mono text-xs" id="gtm" name="seo[google][gtm_container_id]" value="{{ old('seo.google.gtm_container_id', $g['gtm_container_id'] ?? '') }}" placeholder="GTM-XXXXXXX" autocomplete="off">
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="adsense">AdSense publisher ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[google][adsense_publisher_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[google][adsense_publisher_id_active]" id="adsense-active" value="1" @checked(old('seo.google.adsense_publisher_id_active', $g['adsense_publisher_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="adsense-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input font-mono text-xs" id="adsense" name="seo[google][adsense_publisher_id]" value="{{ old('seo.google.adsense_publisher_id', $g['adsense_publisher_id'] ?? '') }}" placeholder="ca-pub-XXXXXXXX" autocomplete="off">
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="gsc">Search Console verification</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[google][search_console_verification_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[google][search_console_verification_active]" id="gsc-active" value="1" @checked(old('seo.google.search_console_verification_active', $g['search_console_verification_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="gsc-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input font-mono text-xs" id="gsc" name="seo[google][search_console_verification]" value="{{ old('seo.google.search_console_verification', $g['search_console_verification'] ?? '') }}" autocomplete="off">
						<div class="admin-field-hint">Content value for <code>google-site-verification</code> meta tag.</div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-twitter" role="tabpanel">
				<div class="flex items-center gap-3 py-1 mb-4">
					<input type="hidden" name="seo[twitter][enabled]" value="0">
					<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[twitter][enabled]" id="twitter-enabled" value="1" @checked(old('seo.twitter.enabled', $t['enabled'] ?? false))>
					<label class="text-sm font-semibold text-zinc-900" for="twitter-enabled">Enable Twitter / X cards</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="tw-card">Card type</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[twitter][card_type_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[twitter][card_type_active]" id="tw-card-active" value="1" @checked(old('seo.twitter.card_type_active', $t['card_type_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="tw-card-active">On</label>
							</div>
						</div>
						<select class="admin-field-input" id="tw-card" name="seo[twitter][card_type]">
							@php $card = old('seo.twitter.card_type', $t['card_type'] ?? 'summary_large_image'); @endphp
							<option value="summary" @selected($card === 'summary')>summary</option>
							<option value="summary_large_image" @selected($card === 'summary_large_image')>summary_large_image</option>
						</select>
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="tw-site">Site handle</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[twitter][site_handle_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[twitter][site_handle_active]" id="tw-site-active" value="1" @checked(old('seo.twitter.site_handle_active', $t['site_handle_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="tw-site-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input" id="tw-site" name="seo[twitter][site_handle]" value="{{ old('seo.twitter.site_handle', $t['site_handle'] ?? '') }}" placeholder="@brand" autocomplete="off">
						<div class="admin-field-hint"><code>twitter:site</code> — @ optional.</div>
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="tw-creator">Creator handle</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[twitter][creator_handle_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[twitter][creator_handle_active]" id="tw-creator-active" value="1" @checked(old('seo.twitter.creator_handle_active', $t['creator_handle_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="tw-creator-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input" id="tw-creator" name="seo[twitter][creator_handle]" value="{{ old('seo.twitter.creator_handle', $t['creator_handle'] ?? '') }}" placeholder="@author" autocomplete="off">
						<div class="admin-field-hint"><code>twitter:creator</code></div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-tiktok" role="tabpanel">
				<div class="flex items-center gap-3 py-1 mb-4">
					<input type="hidden" name="seo[tiktok][enabled]" value="0">
					<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[tiktok][enabled]" id="tiktok-enabled" value="1" @checked(old('seo.tiktok.enabled', $tt['enabled'] ?? false))>
					<label class="text-sm font-semibold text-zinc-900" for="tiktok-enabled">Enable TikTok Pixel</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="tt-pixel">TikTok Pixel ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[tiktok][pixel_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[tiktok][pixel_id_active]" id="tt-pixel-active" value="1" @checked(old('seo.tiktok.pixel_id_active', $tt['pixel_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="tt-pixel-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input font-mono text-xs" id="tt-pixel" name="seo[tiktok][pixel_id]" value="{{ old('seo.tiktok.pixel_id', $tt['pixel_id'] ?? '') }}" autocomplete="off">
					</div>
					<div class="md:col-span-2 min-w-0">
						<label class="admin-field-label" for="tt-events">Event tracking settings (JSON)</label>
						<textarea class="admin-field-input font-mono text-xs small" id="tt-events" name="seo[tiktok][event_settings]" rows="4" placeholder="{}">{{ old('seo.tiktok.event_settings', $tt['event_settings'] ?? '') }}</textarea>
						<div class="admin-field-hint">Optional JSON for advanced pixel configuration. Invalid JSON is cleared on save.</div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-additional" role="tabpanel">
				<div class="flex items-center gap-3 py-1 mb-4">
					<input type="hidden" name="seo[additional][enabled]" value="0">
					<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[additional][enabled]" id="add-enabled" value="1" @checked(old('seo.additional.enabled', $a['enabled'] ?? false))>
					<label class="text-sm font-semibold text-zinc-900" for="add-enabled">Enable additional tags</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="li">LinkedIn Insight Tag partner ID</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[additional][linkedin_partner_id_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[additional][linkedin_partner_id_active]" id="li-active" value="1" @checked(old('seo.additional.linkedin_partner_id_active', $a['linkedin_partner_id_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="li-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input" id="li" name="seo[additional][linkedin_partner_id]" value="{{ old('seo.additional.linkedin_partner_id', $a['linkedin_partner_id'] ?? '') }}" inputmode="numeric" autocomplete="off">
					</div>
					<div class="min-w-0">
						<div class="mb-1 flex items-center justify-between gap-2">
							<label class="admin-field-label mb-0" for="pin">Pinterest domain verification</label>
							<div class="flex items-center gap-2 shrink-0">
								<input type="hidden" name="seo[additional][pinterest_verification_active]" value="0">
								<input type="checkbox" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" name="seo[additional][pinterest_verification_active]" id="pin-active" value="1" @checked(old('seo.additional.pinterest_verification_active', $a['pinterest_verification_active'] ?? true))>
								<label class="text-sm text-zinc-700" for="pin-active">On</label>
							</div>
						</div>
						<input type="text" class="admin-field-input" id="pin" name="seo[additional][pinterest_verification]" value="{{ old('seo.additional.pinterest_verification', $a['pinterest_verification'] ?? '') }}" autocomplete="off">
						<div class="admin-field-hint">Value for <code>p:domain_verify</code> meta tag.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="flex flex-wrap items-center gap-3 border-t border-zinc-100 pt-6">
		<x-admin.button variant="primary" type="submit">Save SEO settings</x-admin.button>
		<x-admin.button variant="secondary" :href="route('admin.dashboard')">Cancel</x-admin.button>
	</div>
</form>
	</x-admin.card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
		new bootstrap.Tooltip(el);
	});
});
</script>
@endpush
