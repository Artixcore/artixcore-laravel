@extends('layouts.admin')
@section('title', 'Message')
@section('content')
	<div class="mb-6">
		<a
			href="{{ route('admin.contact-messages.index') }}"
			class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
		>← Back to inbox</a>
	</div>
	<x-admin.card>
		<p class="text-sm text-zinc-500">
			{{ $message->created_at }}
			@if ($message->ip_address)
				· {{ $message->ip_address }}
			@endif
		</p>
		<h1 class="mt-2 text-lg font-semibold text-zinc-900">{{ $message->name }} &lt;{{ $message->email }}&gt;</h1>
		@if ($message->company)
			<p class="mt-2 text-sm text-zinc-600"><span class="font-medium text-zinc-800">Company:</span> {{ $message->company }}</p>
		@endif
		@if ($message->phone)
			<p class="mt-2 text-sm text-zinc-600"><span class="font-medium text-zinc-800">Phone:</span> {{ $message->phone }}</p>
		@endif
		<hr class="my-6 border-zinc-100">
		<p class="whitespace-pre-wrap text-sm leading-relaxed text-zinc-800">{{ $message->message }}</p>
	</x-admin.card>
@endsection
