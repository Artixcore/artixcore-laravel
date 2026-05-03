@extends('layouts.app')

@php
	$siteName = $site->site_name ?? 'Artixcore';
	$pageTitle = ($article->meta_title ?: $article->title).' — '.$siteName;
	$pageDesc = $article->meta_description ?? $article->summary ?? '';
	$keywords = $article->meta_keywords ?? '';
	$canonical = $article->canonical_url ?: route('articles.show', $article->slug);
	$ogImage = str_starts_with($article->main_image_url, 'http') ? $article->main_image_url : url($article->main_image_url);
	$shareUrl = $canonical;
@endphp

@section('meta_title', $pageTitle)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 160))
@section('meta_keywords', $keywords)
@section('meta_robots', $article->robots ?: 'index,follow')
@section('canonical_url', $canonical)

@section('og_title', ($article->meta_title ?: $article->title).' — '.$siteName)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags((string) $pageDesc), 200))
@section('og_image', $ogImage)
@section('og_type', 'article')

@push('jsonld')
@php
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	$articleLd = [
		'@context' => 'https://schema.org',
		'@type' => 'Article',
		'headline' => $article->title,
		'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($article->summary ?? '')), 300),
		'image' => [$ogImage],
		'datePublished' => $article->published_at?->toIso8601String(),
		'dateModified' => $article->updated_at?->toIso8601String(),
		'author' => ['@type' => 'Person', 'name' => $article->author_name ?: 'Ali 1.0'],
		'publisher' => ['@type' => 'Organization', 'name' => $siteName],
		'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
	];
	$breadcrumb = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => [
			['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
			['@type' => 'ListItem', 'position' => 2, 'name' => 'Articles', 'item' => route('articles.index')],
			['@type' => 'ListItem', 'position' => 3, 'name' => $article->title, 'item' => $canonical],
		],
	];
@endphp
<script type="application/ld+json">{!! json_encode($articleLd, $jsonFlags) !!}</script>
<script type="application/ld+json">{!! json_encode($breadcrumb, $jsonFlags) !!}</script>
@endpush

@section('content')
@include('pages.articles.partials.detail', [
	'article' => $article,
	'articleBodyHtml' => $articleBodyHtml,
	'toc' => $toc,
	'relatedArticles' => $relatedArticles,
	'shareUrl' => $shareUrl,
	'linkedServices' => $linkedServices ?? collect(),
	'linkedPlatforms' => $linkedPlatforms ?? collect(),
	'relatedCaseStudiesGraph' => $relatedCaseStudiesGraph ?? collect(),
])
@endsection
