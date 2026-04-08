@extends('layouts.app')

@section('meta_title', ($article->meta_title ?: $article->title).' — '.($site->site_name ?? 'Artixcore'))
@section('meta_description', $article->meta_description ?? $article->summary)

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<article class="mx-auto" style="max-width: 720px;">
			<nav class="mb-3">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
					<li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
					<li class="breadcrumb-item active">{{ $article->title }}</li>
				</ol>
			</nav>
			<h1 class="mb-3">{{ $article->title }}</h1>
			<p class="text-muted small mb-4">{{ optional($article->published_at)->format('F j, Y') }}</p>
			<div class="prose-lg">
				{!! $article->body !!}
			</div>
		</article>
	</div>
</section>
@endsection
