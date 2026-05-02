@extends('layouts.app')

@section('meta_title', $about['meta_title'] ?? config('marketing.about.meta_title'))
@section('meta_description', $about['meta_description'] ?? config('marketing.about.meta_description'))
@section('og_title', $about['og_title'] ?? ($about['meta_title'] ?? config('marketing.about.meta_title')))
@section('og_description', $about['og_description'] ?? ($about['meta_description'] ?? config('marketing.about.meta_description')))

@push('jsonld')
@php
	$breadcrumbs = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => [
			[
				'@type' => 'ListItem',
				'position' => 1,
				'name' => 'Home',
				'item' => url('/'),
			],
			[
				'@type' => 'ListItem',
				'position' => 2,
				'name' => $about['page_title'] ?? 'About',
			],
		],
	];
	$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbs, $jsonFlags) !!}</script>
@endpush

@section('content')
<section class="pt-8 pb-0">
	<div class="container">
		<nav class="mb-3" aria-label="Breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
				<li class="breadcrumb-item active">About</li>
			</ol>
		</nav>
		<h1 class="mb-4">{{ $about['page_title'] ?? 'About' }}</h1>
		<p class="lead">{{ $about['lead'] ?? '' }}</p>
	</div>
</section>
<section class="pb-5">
	<div class="container">
		<div class="row g-5">
			<div class="col-lg-6">
				<h3>{{ $about['mission_title'] ?? 'Mission' }}</h3>
				<p>{{ $about['mission_body'] ?? '' }}</p>
			</div>
			<div class="col-lg-6">
				<h3>{{ $about['vision_title'] ?? 'Vision' }}</h3>
				<p>{{ $about['vision_body'] ?? '' }}</p>
			</div>
		</div>
		<div class="prose-lg mt-5">
			{!! $about['body_html'] ?? '' !!}
		</div>
	</div>
</section>
@endsection
