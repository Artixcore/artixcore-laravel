@php
	$pageOgTitle = $__env->hasSection('og_title') ? trim($__env->yieldContent('og_title')) : '';
	$ogTitle = $pageOgTitle !== '' ? $pageOgTitle : ($seoHead['og_title'] ?? '');
	$pageOgDesc = $__env->hasSection('og_description') ? trim($__env->yieldContent('og_description')) : '';
	$ogDescription = $pageOgDesc !== '' ? $pageOgDesc : ($seoHead['og_description'] ?? '');
	$pageOgImage = $__env->hasSection('og_image') ? trim($__env->yieldContent('og_image')) : '';
	$ogImage = $pageOgImage !== '' ? $pageOgImage : ($seoHead['og_image'] ?? null);
	$tw = $seoHead['twitter'] ?? ['card' => 'summary_large_image', 'site' => null, 'creator' => null];
	$canonical = $__env->hasSection('canonical_url')
		? trim($__env->yieldContent('canonical_url'))
		: '';
	if ($canonical === '') {
		$canonical = url()->current();
	}
	$pageTwTitle = $__env->hasSection('twitter_title') ? trim($__env->yieldContent('twitter_title')) : '';
	$twTitle = $pageTwTitle !== '' ? $pageTwTitle : $ogTitle;
	$pageTwDesc = $__env->hasSection('twitter_description') ? trim($__env->yieldContent('twitter_description')) : '';
	$twDescription = $pageTwDesc !== '' ? $pageTwDesc : $ogDescription;
	$pageTwImage = $__env->hasSection('twitter_image') ? trim($__env->yieldContent('twitter_image')) : '';
	$twImage = $pageTwImage !== '' ? $pageTwImage : $ogImage;
@endphp
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
	$ogType = $__env->hasSection('og_type') ? trim($__env->yieldContent('og_type')) : '';
	if ($ogType === '') {
		$ogType = $seoHead['og_type'] ?? 'website';
	}
@endphp
<meta property="og:type" content="{{ e($ogType) }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ e($ogTitle) }}">
@if($ogDescription !== '')
<meta property="og:description" content="{{ e($ogDescription) }}">
@endif
@if(!empty($ogImage))
<meta property="og:image" content="{{ e($ogImage) }}">
@endif
@if(!empty($seoHead['fb_app_id']))
<meta property="fb:app_id" content="{{ e($seoHead['fb_app_id']) }}">
@endif
<meta name="twitter:card" content="{{ e($tw['card'] ?? 'summary_large_image') }}">
<meta name="twitter:title" content="{{ e($twTitle) }}">
@if($twDescription !== '')
<meta name="twitter:description" content="{{ e($twDescription) }}">
@endif
@if(!empty($twImage))
<meta name="twitter:image" content="{{ e($twImage) }}">
@endif
@if(!empty($tw['site']))
<meta name="twitter:site" content="{{ e(str_starts_with((string) $tw['site'], '@') ? $tw['site'] : '@'.$tw['site']) }}">
@endif
@if(!empty($tw['creator']))
<meta name="twitter:creator" content="{{ e(str_starts_with((string) $tw['creator'], '@') ? $tw['creator'] : '@'.$tw['creator']) }}">
@endif
@if(!empty($seoHead['google_site_verification']))
<meta name="google-site-verification" content="{{ e($seoHead['google_site_verification']) }}">
@endif
@if(!empty($seoHead['pinterest_verification']))
<meta name="p:domain_verify" content="{{ e($seoHead['pinterest_verification']) }}">
@endif
