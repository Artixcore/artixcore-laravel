@extends('layouts.intake')

@section('meta_title', $meta_title ?? 'Tell us about your needs')
@section('meta_description', $meta_description ?? '')

@section('content')
	<div
		id="intake-root"
		class="intake-root"
		data-store-url="{{ route('get-started.store') }}"
		data-chat-url="{{ url('/api/v1/ai/chat') }}"
	></div>
@endsection
