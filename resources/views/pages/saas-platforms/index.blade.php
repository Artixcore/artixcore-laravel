@extends('layouts.app')

@php $p = $saasPage; @endphp
@section('meta_title', $p['meta_title'] ?? config('marketing.saas.meta_title'))
@section('meta_description', $p['meta_description'] ?? config('marketing.saas.meta_description'))
@section('og_title', $p['og_title'] ?? ($p['meta_title'] ?? config('marketing.saas.meta_title')))
@section('og_description', $p['og_description'] ?? ($p['meta_description'] ?? config('marketing.saas.meta_description')))

@push('vendor_styles')
<link rel="stylesheet" href="{{ asset('theme/vendor/aos/aos.css') }}">
@endpush

@section('content')
@include('pages.saas-platforms._hero')
@include('pages.saas-platforms._stats')
@include('pages.saas-platforms._overview')
@include('pages.saas-platforms._offerings')
@include('pages.saas-platforms._why')
@include('pages.saas-platforms._features')
@include('pages.saas-platforms._process')
@include('pages.saas-platforms._use_cases')
@include('pages.saas-platforms._trust')
@include('pages.saas-platforms._services_highlight')
@include('pages.saas-platforms._case_studies')
@include('pages.saas-platforms._testimonials')
@include('pages.saas-platforms._faq')
@include('pages.saas-platforms._cta')
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
