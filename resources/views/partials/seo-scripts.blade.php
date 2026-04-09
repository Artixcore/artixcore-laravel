@once('seo-tracking-scripts')
@php
	$ga4 = trim($seoScripts['ga4_measurement_id'] ?? '');
	$ads = trim($seoScripts['adsense_publisher_id'] ?? '');
	$mp = trim($seoScripts['meta_pixel_id'] ?? '');
	$ttp = trim($seoScripts['tiktok_pixel_id'] ?? '');
	$li = trim($seoScripts['linkedin_partner_id'] ?? '');
@endphp
@if($ads !== '')
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ e($ads) }}" crossorigin="anonymous"></script>
@endif
@if($ga4 !== '')
<script async src="https://www.googletagmanager.com/gtag/js?id={{ e($ga4) }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', @json($ga4));
</script>
@endif
@if($mp !== '')
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', @json($mp));
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id={{ e($mp) }}&ev=PageView&noscript=1"></noscript>
@endif
@if($ttp !== '')
<script>
!function (w, d, t) {
	w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script");n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
	ttq.load(@json($ttp));
	ttq.page();
}(window, document, 'ttq');
</script>
@endif
@if($li !== '')
<script type="text/javascript">
var _linkedin_partner_id = @json($li);
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script><script type="text/javascript" async src="https://snap.licdn.com/li.lms-analytics/insight.min.js"></script>
<noscript><img height="1" width="1" style="display:none" alt="" src="https://px.ads.linkedin.com/collect/?pid={{ e($li) }}&fmt=gif"></noscript>
@endif
@endonce
