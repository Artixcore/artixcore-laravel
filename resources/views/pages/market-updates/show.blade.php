@extends('layouts.app')

@php
	$siteName = $site->site_name ?? 'Artixcore';
	$pageTitle = ($update->meta_title ?: $update->title).' — '.$siteName;
	$pageDesc = $update->meta_description ?? $update->excerpt ?? '';
	$canonical = $update->canonical_url ?: route('market-updates.show', $update->slug);
	$ogImage = str_starts_with($update->main_image_url, 'http') ? $update->main_image_url : url($update->main_image_url);
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 160))
@section('meta_keywords', $update->meta_keywords ?? '')
@section('meta_robots', $update->robots ?: 'index,follow')
@section('canonical_url', $canonical)
@section('og_title', $pageTitle)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 200))
@section('og_image', $ogImage)
@section('og_type', 'article')

@push('jsonld')
@php
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	$articleLd = [
		'@context' => 'https://schema.org',
		'@type' => 'Article',
		'headline' => $update->title,
		'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($update->excerpt ?? '')), 300),
		'image' => [$ogImage],
		'datePublished' => $update->published_at?->toIso8601String(),
		'dateModified' => $update->updated_at?->toIso8601String(),
		'author' => ['@type' => 'Person', 'name' => $update->author_name ?: 'Ali 1.0'],
		'publisher' => ['@type' => 'Organization', 'name' => $siteName],
		'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
	];
	$breadcrumb = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => [
			['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
			['@type' => 'ListItem', 'position' => 2, 'name' => 'Market updates', 'item' => route('market-updates.index')],
			['@type' => 'ListItem', 'position' => 3, 'name' => $update->title, 'item' => $canonical],
		],
	];
@endphp
<script type="application/ld+json">{!! json_encode($articleLd, $jsonFlags) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, $jsonFlags) !!}</script>
@endpush

@section('content')
@include('pages.market-updates.partials.public-detail', [
	'update' => $update,
	'bodyHtml' => $bodyHtml,
	'sectionsHtml' => $sectionsHtml,
	'relatedMarketUpdates' => $relatedMarketUpdates,
	'videoEmbed' => $videoEmbed ?? null,
])
@endsection
