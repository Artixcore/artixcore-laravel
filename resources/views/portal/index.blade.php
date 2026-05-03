@extends('layouts.app')

@section('meta_title', 'Portal — '.config('app.name'))

@section('content')
	<div class="container py-16">
		<h1 class="text-3xl font-bold text-slate-900">Welcome, {{ auth()->user()->name }}</h1>
		<p class="mt-3 text-slate-600">Use the API portal for profile and settings:</p>
		<ul class="mt-6 list-disc space-y-2 pl-6 text-slate-700">
			<li><code class="rounded bg-slate-100 px-1">GET /api/v1/portal/me</code> with a Sanctum token from API login</li>
			<li>Session portal is for quick access; token-based tools use Sanctum as documented in CONTEXT.</li>
		</ul>
		<form method="post" action="{{ route('logout') }}" class="mt-10">
			@csrf
			<button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Sign out</button>
		</form>
	</div>
@endsection
