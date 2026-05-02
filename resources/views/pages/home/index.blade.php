@extends('layouts.app')

@section('meta_title', config('marketing.homepage.meta_title'))
@section('meta_description', config('marketing.homepage.meta_description'))
@section('og_title', config('marketing.homepage.og_title'))
@section('og_description', config('marketing.homepage.og_description'))

@push('vendor_styles')
<link rel="stylesheet" href="{{ asset('theme/vendor/aos/aos.css') }}">
@endpush

@section('content')
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
