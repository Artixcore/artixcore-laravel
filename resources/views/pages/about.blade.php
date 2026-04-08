@extends('layouts.app')

@section('meta_title', $about['meta_title'] ?? 'About — Artixcore')
@section('meta_description', $about['meta_description'] ?? '')

@section('content')
<section class="pt-8 pb-0">
	<div class="container">
		<nav class="mb-3">
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
