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
<h1 class="h4 mb-3">SEO settings</h1>
<p class="text-muted small mb-4">Integrations and default social meta. Open Graph defaults fall back to <a href="{{ route('admin.site-settings.edit') }}">Site settings</a> when overrides are empty.</p>

<form method="post" action="{{ route('admin.seo-settings.update') }}" class="card border-0 shadow-sm" id="seo-settings-form">
	@csrf
	@method('PUT')
	@if($errors->any())
		<div class="alert alert-danger m-3 mb-0 small">
			<ul class="mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
		</div>
	@endif
	<div class="card-body">
		<ul class="nav nav-tabs mb-3" id="seo-tabs" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="tab-meta" data-bs-toggle="tab" data-bs-target="#pane-meta" type="button" role="tab">Meta (Facebook)</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="tab-google" data-bs-toggle="tab" data-bs-target="#pane-google" type="button" role="tab">Google</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="tab-twitter" data-bs-toggle="tab" data-bs-target="#pane-twitter" type="button" role="tab">Twitter (X)</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="tab-tiktok" data-bs-toggle="tab" data-bs-target="#pane-tiktok" type="button" role="tab">TikTok</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="tab-additional" data-bs-toggle="tab" data-bs-target="#pane-additional" type="button" role="tab">Additional</button>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade show active" id="pane-meta" role="tabpanel">
				<div class="form-check form-switch mb-4">
					<input type="hidden" name="seo[meta][enabled]" value="0">
					<input type="checkbox" class="form-check-input" name="seo[meta][enabled]" id="meta-enabled" value="1" @checked(old('seo.meta.enabled', $m['enabled'] ?? false))>
					<label class="form-check-label fw-semibold" for="meta-enabled">Enable Meta integration</label>
					<div class="form-text">Master switch for Meta Pixel, App ID, and OG overrides below.</div>
				</div>
				<div class="row g-3">
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="meta-pixel">Meta Pixel ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[meta][pixel_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[meta][pixel_id_active]" id="meta-pixel-active" value="1" @checked(old('seo.meta.pixel_id_active', $m['pixel_id_active'] ?? true))>
								<label class="form-check-label" for="meta-pixel-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control" id="meta-pixel" name="seo[meta][pixel_id]" value="{{ old('seo.meta.pixel_id', $m['pixel_id'] ?? '') }}" inputmode="numeric" autocomplete="off">
						<div class="form-text">Numeric ID from Meta Events Manager. <span class="text-muted" data-bs-toggle="tooltip" data-bs-title="Loaded only when integration is enabled and this field is on.">?</span></div>
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="meta-app">App ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[meta][app_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[meta][app_id_active]" id="meta-app-active" value="1" @checked(old('seo.meta.app_id_active', $m['app_id_active'] ?? true))>
								<label class="form-check-label" for="meta-app-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control" id="meta-app" name="seo[meta][app_id]" value="{{ old('seo.meta.app_id', $m['app_id'] ?? '') }}" autocomplete="off">
						<div class="form-text">Used for <code>fb:app_id</code> Open Graph meta. <span class="text-muted" data-bs-toggle="tooltip" data-bs-title="Optional; enables Facebook app attribution in shared links.">?</span></div>
					</div>
					<div class="col-12"><hr class="text-muted"></div>
					<div class="col-12">
						<h2 class="h6 text-muted">Open Graph overrides</h2>
						<p class="small text-muted">Leave blank to use Site settings defaults.</p>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="meta-og-title">Default OG title</label>
						<input type="text" class="form-control" id="meta-og-title" name="seo[meta][og_title_override]" value="{{ old('seo.meta.og_title_override', $m['og_title_override'] ?? '') }}" maxlength="255">
					</div>
					<div class="col-md-6">
						<label class="form-label" for="meta-og-img">Default OG image URL</label>
						<input type="url" class="form-control" id="meta-og-img" name="seo[meta][og_image_url]" value="{{ old('seo.meta.og_image_url', $m['og_image_url'] ?? '') }}" placeholder="https://…">
						<div class="form-text">Full HTTPS URL. Overrides uploaded OG image in Site settings when set.</div>
					</div>
					<div class="col-12">
						<label class="form-label" for="meta-og-desc">Default OG description</label>
						<textarea class="form-control" id="meta-og-desc" name="seo[meta][og_description_override]" rows="3" maxlength="2000">{{ old('seo.meta.og_description_override', $m['og_description_override'] ?? '') }}</textarea>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-google" role="tabpanel">
				<div class="form-check form-switch mb-4">
					<input type="hidden" name="seo[google][enabled]" value="0">
					<input type="checkbox" class="form-check-input" name="seo[google][enabled]" id="google-enabled" value="1" @checked(old('seo.google.enabled', $g['enabled'] ?? false))>
					<label class="form-check-label fw-semibold" for="google-enabled">Enable Google integration</label>
				</div>
				<div class="row g-3">
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="ga4">GA4 measurement ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[google][ga4_measurement_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[google][ga4_measurement_id_active]" id="ga4-active" value="1" @checked(old('seo.google.ga4_measurement_id_active', $g['ga4_measurement_id_active'] ?? true))>
								<label class="form-check-label" for="ga4-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control font-monospace" id="ga4" name="seo[google][ga4_measurement_id]" value="{{ old('seo.google.ga4_measurement_id', $g['ga4_measurement_id'] ?? '') }}" placeholder="G-XXXXXXXXXX" autocomplete="off">
						<div class="form-text">Format <code>G-…</code>. Loads gtag when enabled. <span class="text-muted" data-bs-toggle="tooltip" data-bs-title="Avoid duplicating the same property in GTM if you use both.">?</span></div>
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="gtm">Google Tag Manager ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[google][gtm_container_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[google][gtm_container_id_active]" id="gtm-active" value="1" @checked(old('seo.google.gtm_container_id_active', $g['gtm_container_id_active'] ?? true))>
								<label class="form-check-label" for="gtm-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control font-monospace" id="gtm" name="seo[google][gtm_container_id]" value="{{ old('seo.google.gtm_container_id', $g['gtm_container_id'] ?? '') }}" placeholder="GTM-XXXXXXX" autocomplete="off">
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="adsense">AdSense publisher ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[google][adsense_publisher_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[google][adsense_publisher_id_active]" id="adsense-active" value="1" @checked(old('seo.google.adsense_publisher_id_active', $g['adsense_publisher_id_active'] ?? true))>
								<label class="form-check-label" for="adsense-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control font-monospace" id="adsense" name="seo[google][adsense_publisher_id]" value="{{ old('seo.google.adsense_publisher_id', $g['adsense_publisher_id'] ?? '') }}" placeholder="ca-pub-XXXXXXXX" autocomplete="off">
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="gsc">Search Console verification</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[google][search_console_verification_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[google][search_console_verification_active]" id="gsc-active" value="1" @checked(old('seo.google.search_console_verification_active', $g['search_console_verification_active'] ?? true))>
								<label class="form-check-label" for="gsc-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control font-monospace" id="gsc" name="seo[google][search_console_verification]" value="{{ old('seo.google.search_console_verification', $g['search_console_verification'] ?? '') }}" autocomplete="off">
						<div class="form-text">Content value for <code>google-site-verification</code> meta tag.</div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-twitter" role="tabpanel">
				<div class="form-check form-switch mb-4">
					<input type="hidden" name="seo[twitter][enabled]" value="0">
					<input type="checkbox" class="form-check-input" name="seo[twitter][enabled]" id="twitter-enabled" value="1" @checked(old('seo.twitter.enabled', $t['enabled'] ?? false))>
					<label class="form-check-label fw-semibold" for="twitter-enabled">Enable Twitter / X cards</label>
				</div>
				<div class="row g-3">
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="tw-card">Card type</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[twitter][card_type_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[twitter][card_type_active]" id="tw-card-active" value="1" @checked(old('seo.twitter.card_type_active', $t['card_type_active'] ?? true))>
								<label class="form-check-label" for="tw-card-active">On</label>
							</div>
						</div>
						<select class="form-select" id="tw-card" name="seo[twitter][card_type]">
							@php $card = old('seo.twitter.card_type', $t['card_type'] ?? 'summary_large_image'); @endphp
							<option value="summary" @selected($card === 'summary')>summary</option>
							<option value="summary_large_image" @selected($card === 'summary_large_image')>summary_large_image</option>
						</select>
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="tw-site">Site handle</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[twitter][site_handle_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[twitter][site_handle_active]" id="tw-site-active" value="1" @checked(old('seo.twitter.site_handle_active', $t['site_handle_active'] ?? true))>
								<label class="form-check-label" for="tw-site-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control" id="tw-site" name="seo[twitter][site_handle]" value="{{ old('seo.twitter.site_handle', $t['site_handle'] ?? '') }}" placeholder="@brand" autocomplete="off">
						<div class="form-text"><code>twitter:site</code> — @ optional.</div>
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="tw-creator">Creator handle</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[twitter][creator_handle_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[twitter][creator_handle_active]" id="tw-creator-active" value="1" @checked(old('seo.twitter.creator_handle_active', $t['creator_handle_active'] ?? true))>
								<label class="form-check-label" for="tw-creator-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control" id="tw-creator" name="seo[twitter][creator_handle]" value="{{ old('seo.twitter.creator_handle', $t['creator_handle'] ?? '') }}" placeholder="@author" autocomplete="off">
						<div class="form-text"><code>twitter:creator</code></div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-tiktok" role="tabpanel">
				<div class="form-check form-switch mb-4">
					<input type="hidden" name="seo[tiktok][enabled]" value="0">
					<input type="checkbox" class="form-check-input" name="seo[tiktok][enabled]" id="tiktok-enabled" value="1" @checked(old('seo.tiktok.enabled', $tt['enabled'] ?? false))>
					<label class="form-check-label fw-semibold" for="tiktok-enabled">Enable TikTok Pixel</label>
				</div>
				<div class="row g-3">
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="tt-pixel">TikTok Pixel ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[tiktok][pixel_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[tiktok][pixel_id_active]" id="tt-pixel-active" value="1" @checked(old('seo.tiktok.pixel_id_active', $tt['pixel_id_active'] ?? true))>
								<label class="form-check-label" for="tt-pixel-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control font-monospace" id="tt-pixel" name="seo[tiktok][pixel_id]" value="{{ old('seo.tiktok.pixel_id', $tt['pixel_id'] ?? '') }}" autocomplete="off">
					</div>
					<div class="col-12">
						<label class="form-label" for="tt-events">Event tracking settings (JSON)</label>
						<textarea class="form-control font-monospace small" id="tt-events" name="seo[tiktok][event_settings]" rows="4" placeholder="{}">{{ old('seo.tiktok.event_settings', $tt['event_settings'] ?? '') }}</textarea>
						<div class="form-text">Optional JSON for advanced pixel configuration. Invalid JSON is cleared on save.</div>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="pane-additional" role="tabpanel">
				<div class="form-check form-switch mb-4">
					<input type="hidden" name="seo[additional][enabled]" value="0">
					<input type="checkbox" class="form-check-input" name="seo[additional][enabled]" id="add-enabled" value="1" @checked(old('seo.additional.enabled', $a['enabled'] ?? false))>
					<label class="form-check-label fw-semibold" for="add-enabled">Enable additional tags</label>
				</div>
				<div class="row g-3">
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="li">LinkedIn Insight Tag partner ID</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[additional][linkedin_partner_id_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[additional][linkedin_partner_id_active]" id="li-active" value="1" @checked(old('seo.additional.linkedin_partner_id_active', $a['linkedin_partner_id_active'] ?? true))>
								<label class="form-check-label" for="li-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control" id="li" name="seo[additional][linkedin_partner_id]" value="{{ old('seo.additional.linkedin_partner_id', $a['linkedin_partner_id'] ?? '') }}" inputmode="numeric" autocomplete="off">
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<label class="form-label mb-0" for="pin">Pinterest domain verification</label>
							<div class="form-check form-switch form-check-reverse small">
								<input type="hidden" name="seo[additional][pinterest_verification_active]" value="0">
								<input type="checkbox" class="form-check-input" name="seo[additional][pinterest_verification_active]" id="pin-active" value="1" @checked(old('seo.additional.pinterest_verification_active', $a['pinterest_verification_active'] ?? true))>
								<label class="form-check-label" for="pin-active">On</label>
							</div>
						</div>
						<input type="text" class="form-control" id="pin" name="seo[additional][pinterest_verification]" value="{{ old('seo.additional.pinterest_verification', $a['pinterest_verification'] ?? '') }}" autocomplete="off">
						<div class="form-text">Value for <code>p:domain_verify</code> meta tag.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card-footer bg-white border-top-0 d-flex gap-2">
		<button type="submit" class="btn btn-primary">Save SEO settings</button>
		<a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
	</div>
</form>
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
