@extends('layouts.app')

@php
	$siteName = $site->site_name ?? 'Artixcore';
	$pageTitle = ($study->meta_title ?: $study->title).' — '.$siteName;
	$pageDesc = $study->meta_description ?? $study->summary ?? '';
	$keywords = $study->meta_keywords ?? '';
	$canonical = $study->canonical_url ?: route('case-studies.show', $study->slug);
	$ogImage = str_starts_with($study->main_image_url, 'http') ? $study->main_image_url : url($study->main_image_url);
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 160))
@section('meta_keywords', $keywords)
@section('meta_robots', $study->robots ?: 'index,follow')
@section('canonical_url', $canonical)

@section('og_title', ($study->meta_title ?: $study->title).' — '.$siteName)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 200))
@section('og_image', $ogImage)
@section('og_type', 'article')

@push('jsonld')
@php
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	$creativeLd = [
		'@context' => 'https://schema.org',
		'@type' => 'CreativeWork',
		'name' => $study->title,
		'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($study->summary ?? '')), 300),
		'image' => [$ogImage],
		'datePublished' => $study->published_at?->toIso8601String(),
		'dateModified' => $study->updated_at?->toIso8601String(),
		'author' => ['@type' => 'Person', 'name' => $study->author_name ?: 'Ali 1.0'],
		'publisher' => ['@type' => 'Organization', 'name' => $siteName],
		'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
	];
	$breadcrumb = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => [
			['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
			['@type' => 'ListItem', 'position' => 2, 'name' => 'Case studies', 'item' => route('case-studies.index')],
			['@type' => 'ListItem', 'position' => 3, 'name' => $study->title, 'item' => $canonical],
		],
	];
@endphp
<script type="application/ld+json">{!! json_encode($creativeLd, $jsonFlags) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, $jsonFlags) !!}</script>
@endpush

@section('content')
@include('pages.case-studies.partials.detail', [
	'study' => $study,
	'challengeHtml' => $challengeHtml,
	'solutionHtml' => $solutionHtml,
	'implementationHtml' => $implementationHtml,
	'lessonsHtml' => $lessonsHtml,
	'bodyHtml' => $bodyHtml,
	'relatedCaseStudies' => $relatedCaseStudies,
	'videoEmbed' => $videoEmbed ?? null,
])
@endsection
