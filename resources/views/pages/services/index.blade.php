@extends('layouts.app')

@php $sp = $servicesPage; @endphp
@section('meta_title', $sp['meta_title'] ?? config('marketing.services.meta_title'))
@section('meta_description', $sp['meta_description'] ?? config('marketing.services.meta_description'))
@section('og_title', $sp['og_title'] ?? ($sp['meta_title'] ?? config('marketing.services.meta_title')))
@section('og_description', $sp['og_description'] ?? ($sp['meta_description'] ?? config('marketing.services.meta_description')))

@push('vendor_styles')
<link rel="stylesheet" href="{{ asset('theme/vendor/aos/aos.css') }}">
@endpush

@section('content')
@include('pages.services._hero')
@include('pages.services._intro')
@include('pages.services._highlights')
@include('pages.services._grid')
@include('pages.services._process')
@include('pages.services._testimonials')
@include('pages.services._cta')
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
