@extends('layouts.app')

@section('meta_title', 'Page not found — '.($site->site_name ?? 'Artixcore'))

@section('content')
<section class="pt-9 pb-9">
	<div class="container text-center">
		<h1 class="display-4">404</h1>
		<p class="lead">This page could not be found.</p>
		<a href="{{ url('/') }}" class="btn btn-primary mb-0">Back home</a>
	</div>
</section>
@endsection
