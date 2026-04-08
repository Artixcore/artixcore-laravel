@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="h3 mb-4">Dashboard</h1>
<div class="row g-3">
	<div class="col-md-3">
		<div class="card border-0 shadow-sm"><div class="card-body">
			<div class="text-muted small">Draft articles</div>
			<div class="h3 mb-0">{{ $draftArticles }}</div>
		</div></div>
	</div>
	<div class="col-md-3">
		<div class="card border-0 shadow-sm"><div class="card-body">
			<div class="text-muted small">Draft case studies</div>
			<div class="h3 mb-0">{{ $draftCaseStudies }}</div>
		</div></div>
	</div>
	<div class="col-md-3">
		<div class="card border-0 shadow-sm"><div class="card-body">
			<div class="text-muted small">Draft services</div>
			<div class="h3 mb-0">{{ $draftServices }}</div>
		</div></div>
	</div>
	<div class="col-md-3">
		<div class="card border-0 shadow-sm"><div class="card-body">
			<div class="text-muted small">Unread messages</div>
			<div class="h3 mb-0">{{ $unreadMessages }}</div>
		</div></div>
	</div>
</div>
@endsection
