@extends('layouts.app')

@section('meta_title', 'Preview: '.($article->meta_title ?: $article->title))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) ($article->summary ?? '')), 160))
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="bg-amber-50 border-b border-amber-100 py-2 text-center text-sm text-amber-900">
	Draft preview — not indexed. <a href="{{ route('admin.articles.edit', $article) }}" class="fw-semibold">Back to edit</a>
</div>
@include('pages.articles.partials.detail', [
	'article' => $article,
	'articleBodyHtml' => $articleBodyHtml,
	'toc' => $toc,
	'relatedArticles' => collect(),
	'shareUrl' => url()->current(),
])
@endsection
