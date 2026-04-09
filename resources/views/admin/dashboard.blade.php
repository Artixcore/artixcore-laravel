@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
	<x-admin.page-header title="Dashboard">
		<x-slot:subtitle>Overview of your content and inbox</x-slot:subtitle>
	</x-admin.page-header>

	<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft articles</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftArticles }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft case studies</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftCaseStudies }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft services</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftServices }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Unread messages</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $unreadMessages }}</p>
		</x-admin.card>
	</div>
@endsection
