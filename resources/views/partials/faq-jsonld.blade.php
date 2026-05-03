@php
	$faqs = $faqs ?? collect();
	if ($faqs->isEmpty()) {
		return;
	}
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	$entities = [];
	foreach ($faqs as $faq) {
		$entities[] = [
			'@type' => 'Question',
			'name' => $faq->question,
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text' => strip_tags((string) $faq->answer),
			],
		];
	}
	$faqLd = [
		'@context' => 'https://schema.org',
		'@type' => 'FAQPage',
		'mainEntity' => $entities,
	];
@endphp
<script type="application/ld+json">{!! json_encode($faqLd, $jsonFlags) !!}</script>
