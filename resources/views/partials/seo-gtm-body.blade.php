@once('seo-gtm-body')
@php $gtm = trim($seoScripts['gtm_container_id'] ?? ''); @endphp
@if($gtm !== '')
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ e($gtm) }}"
height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
@endonce
