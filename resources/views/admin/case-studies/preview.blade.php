@extends('layouts.app')

@section('meta_title', 'Preview: '.($study->meta_title ?: $study->title))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) ($study->summary ?? '')), 160))
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="bg-amber-50 border-b border-amber-100 py-2 text-center text-sm text-amber-900">
	Draft preview — not indexed. <a href="{{ route('admin.case-studies.edit', $study) }}" class="fw-semibold">Back to edit</a>
</div>
@include('pages.case-studies.partials.detail', [
	'study' => $study,
	'challengeHtml' => $challengeHtml,
	'solutionHtml' => $solutionHtml,
	'implementationHtml' => $implementationHtml,
	'lessonsHtml' => $lessonsHtml,
	'bodyHtml' => $bodyHtml,
	'relatedCaseStudies' => $relatedCaseStudies,
	'videoEmbed' => $videoEmbed,
])
@endsection
