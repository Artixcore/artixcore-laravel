@extends('layouts.app')

@section('meta_title', 'Preview: '.($update->meta_title ?: $update->title))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) ($update->excerpt ?? '')), 160))
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="bg-amber-50 border-b border-amber-100 py-2 text-center text-sm text-amber-900">
	Draft preview — not indexed. <a href="{{ route('admin.market-updates.edit', $update) }}" class="fw-semibold">Back to edit</a>
</div>
@include('pages.market-updates.partials.public-detail', [
	'update' => $update,
	'bodyHtml' => $bodyHtml,
	'sectionsHtml' => $sectionsHtml,
	'relatedMarketUpdates' => $relatedMarketUpdates,
	'videoEmbed' => $videoEmbed ?? null,
])
@endsection
