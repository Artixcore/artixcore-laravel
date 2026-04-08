@extends('layouts.app')

@section('meta_title', ($legalPage->meta_title ?: $legalPage->title).' — '.($site->site_name ?? 'Artixcore'))
@section('meta_description', $legalPage->meta_description ?? '')

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<div class="mx-auto prose-lg" style="max-width: 800px;">
			<h1 class="mb-4">{{ $legalPage->title }}</h1>
			{!! $legalPage->body !!}
		</div>
	</div>
</section>
@endsection
