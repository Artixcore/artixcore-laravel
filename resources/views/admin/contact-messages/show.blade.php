@extends('layouts.admin')
@section('title', 'Message')
@section('content')
<div class="mb-3"><a href="{{ route('admin.contact-messages.index') }}" class="small">&larr; Back</a></div>
<div class="card border-0 shadow-sm">
	<div class="card-body">
		<p class="text-muted small mb-1">{{ $message->created_at }} @if($message->ip_address) · {{ $message->ip_address }} @endif</p>
		<h1 class="h5">{{ $message->name }} &lt;{{ $message->email }}&gt;</h1>
		@if($message->company)<p class="small"><strong>Company:</strong> {{ $message->company }}</p>@endif
		@if($message->phone)<p class="small"><strong>Phone:</strong> {{ $message->phone }}</p>@endif
		<hr>
		<p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
	</div>
</div>
@endsection
