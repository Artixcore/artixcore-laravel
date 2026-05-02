@php
	$baseUrl = rtrim((string) config('app.url'), '/');
	$siteName = trim((string) ($site->site_name ?? '')) ?: 'Artixcore';
	$homeUrl = ($baseUrl !== '' ? $baseUrl : rtrim(url('/'), '/')).'/';

	$social = is_array($site->social_links ?? null) ? $site->social_links : [];
	$sameAs = array_values(array_filter(array_map('trim', [
		$social['facebook'] ?? '',
		$social['linkedin'] ?? '',
		$social['twitter'] ?? '',
		$social['instagram'] ?? '',
		$social['youtube'] ?? '',
	]), fn ($u) => $u !== '' && filter_var($u, FILTER_VALIDATE_URL)));

	$organization = [
		'@context' => 'https://schema.org',
		'@type' => 'Organization',
		'name' => $siteName,
		'url' => $homeUrl,
		'description' => (string) config('marketing.organization.description', ''),
	];
	if ($sameAs !== []) {
		$organization['sameAs'] = $sameAs;
	}

	$website = [
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'name' => $siteName,
		'url' => $homeUrl,
	];

	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
@endphp
<script type="application/ld+json">{!! json_encode($organization, $jsonFlags) !!}</script>
<script type="application/ld+json">{!! json_encode($website, $jsonFlags) !!}</script>
@stack('jsonld')
