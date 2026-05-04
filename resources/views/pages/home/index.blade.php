@extends('layouts.app')

@php
	$seo = $homepageSeo ?? [];
@endphp

@section('meta_title', $seo['meta_title'] ?? config('marketing.homepage.meta_title'))
@section('meta_description', $seo['meta_description'] ?? config('marketing.homepage.meta_description'))
@section('meta_keywords', $seo['meta_keywords'] ?? config('marketing.default_keywords'))
@section('canonical_url', $seo['canonical_url'] ?? url('/'))
@section('meta_robots', $seo['robots'] ?? 'index, follow')
@section('og_title', $seo['og_title'] ?? config('marketing.homepage.og_title'))
@section('og_description', $seo['og_description'] ?? config('marketing.homepage.og_description'))
@if(!empty($seo['og_image']))
@section('og_image', $seo['og_image'])
@endif
@if(!empty($seo['twitter_title']))
@section('twitter_title', $seo['twitter_title'])
@endif
@if(!empty($seo['twitter_description']))
@section('twitter_description', $seo['twitter_description'])
@endif
@if(!empty($seo['twitter_image']))
@section('twitter_image', $seo['twitter_image'])
@endif

@push('vendor_styles')
<link rel="stylesheet" href="{{ asset('theme/vendor/aos/aos.css') }}">
@endpush

@section('content')
@if($legacyHome ?? false)
	@include('pages.home._hero')
	@include('pages.home._clients')
	@include('pages.home._intro')
	@include('pages.home._services')
	@include('pages.home._why')
	@include('pages.home._portfolio')
	@include('pages.home._process')
	@include('pages.home._testimonials')
	@include('pages.home._articles')
	@include('pages.home._cta')
	@include('pages.home._contact_teaser')
@else
	@foreach($homepageSections ?? [] as $section)
		@php $partial = $section['partial'] ?? ''; @endphp
		@if($partial !== '' && view()->exists('home.sections.'.$partial))
			@include('home.sections.'.$partial, ['section' => $section])
		@endif
	@endforeach
@endif
@endsection

@push('vendor_scripts')
<script src="{{ asset('theme/vendor/aos/aos.js') }}"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	if (window.AOS) {
		AOS.init({ duration: 800, once: true });
	}
});
</script>
@endpush
