@extends('layouts.app')

@section('meta_title', ($service->title.' — '.($site->site_name ?? 'Artixcore')))
@section('meta_description', $service->summary ?? '')

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<nav class="mb-3">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
				<li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
				<li class="breadcrumb-item active">{{ $service->title }}</li>
			</ol>
		</nav>
		<h1 class="mb-3">{{ $service->title }}</h1>
		@if($service->summary)
			<p class="lead">{{ $service->summary }}</p>
		@endif
		<div class="prose-lg">
			{!! $service->body !!}
		</div>
	</div>
</section>
@endsection
