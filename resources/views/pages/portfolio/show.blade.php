@extends('layouts.app')

@section('meta_title', ($project->meta_title ?: $project->title).' — '.($site->site_name ?? 'Artixcore'))
@section('meta_description', $project->meta_description ?? $project->summary)

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('case-studies.index') }}">Case studies</a></li>
				<li class="breadcrumb-item active">{{ $project->title }}</li>
			</ol>
		</nav>
		<h1 class="mb-3">{{ $project->title }}</h1>
		@if($project->client_name)
			<p class="text-primary fw-semibold">{{ $project->client_name }}</p>
		@endif
		<div class="prose-lg">
			{!! $project->body !!}
		</div>
	</div>
</section>
@endsection
