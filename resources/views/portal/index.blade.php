@extends('layouts.app')

@section('meta_title', 'My portal — '.config('app.name'))
@section('meta_robots', 'noindex, nofollow, noarchive')

@section('content')
<div class="container py-5 py-lg-6">
	<div class="row g-4">
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm">
				<div class="card-body p-4 p-lg-5">
					<h1 class="h3 mb-2">Welcome back, {{ auth()->user()->name }}</h1>
					<p class="text-muted mb-0">Here is a snapshot of your account and quick links to Artixcore.</p>
				</div>
			</div>

			<div class="card border-0 shadow-sm mt-4">
				<div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
					<h2 class="h5 mb-0">Profile</h2>
				</div>
				<div class="card-body p-4">
					<dl class="row mb-0 small">
						<dt class="col-sm-3 text-muted">Email</dt>
						<dd class="col-sm-9">{{ auth()->user()->email }}</dd>
						@if(auth()->user()->company_name)
							<dt class="col-sm-3 text-muted">Company</dt>
							<dd class="col-sm-9">{{ auth()->user()->company_name }}</dd>
						@endif
						@if(auth()->user()->phone)
							<dt class="col-sm-3 text-muted">Phone</dt>
							<dd class="col-sm-9">{{ auth()->user()->phone }}</dd>
						@endif
					</dl>
					<p class="text-muted small mb-0 mt-3">Account settings and profile updates via the API are available if your integration uses Sanctum tokens.</p>
				</div>
			</div>

			<div class="card border-0 shadow-sm mt-4">
				<div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
					<h2 class="h5 mb-0">Quick actions</h2>
				</div>
				<div class="card-body p-4">
					<div class="d-flex flex-wrap gap-2">
						<a href="{{ route('get-started') }}" class="btn btn-primary btn-sm">Start a project</a>
						<a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-sm">View services</a>
						<a href="{{ route('articles.index') }}" class="btn btn-outline-primary btn-sm">Browse articles</a>
						<a href="{{ route('lead.create') }}" class="btn btn-outline-secondary btn-sm">Contact support</a>
					</div>
				</div>
			</div>

			<div class="card border-0 shadow-sm mt-4">
				<div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
					<h2 class="h5 mb-0">My requests</h2>
				</div>
				<div class="card-body p-4">
					@if($myLeads->isEmpty())
						<p class="text-muted mb-0 small">No lead or contact requests match your email yet. When you submit a form using this address, they will appear here.</p>
					@else
						<ul class="list-group list-group-flush">
							@foreach($myLeads as $lead)
								<li class="list-group-item px-0 d-flex justify-content-between align-items-start">
									<div>
										<span class="fw-semibold d-block">{{ $lead->service_type ?? 'Inquiry' }}</span>
										<span class="text-muted small">{{ $lead->created_at?->format('M j, Y') }}</span>
									</div>
									<span class="badge bg-light text-dark text-capitalize">{{ $lead->status ?? 'new' }}</span>
								</li>
							@endforeach
						</ul>
					@endif
				</div>
			</div>

			<div class="card border-0 shadow-sm mt-4">
				<div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
					<h2 class="h5 mb-0">My projects</h2>
				</div>
				<div class="card-body p-4">
					<p class="text-muted small mb-0">Project tracking in the customer portal is coming soon. For active engagements, your team will contact you directly or you can reach us via <a href="{{ route('lead.create') }}">contact</a>.</p>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
					<h2 class="h6 mb-0">Recommended services</h2>
				</div>
				<div class="card-body p-4">
					@if($recommendedServices->isEmpty())
						<p class="text-muted small mb-0">Services will appear here when published.</p>
					@else
						<ul class="list-unstyled mb-0 small">
							@foreach($recommendedServices as $service)
								<li class="mb-2">
									<a href="{{ route('services.show', $service->slug) }}" class="text-decoration-none">{{ $service->title }}</a>
								</li>
							@endforeach
						</ul>
					@endif
				</div>
			</div>

			<div class="card border-0 shadow-sm mb-4">
				<div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
					<h2 class="h6 mb-0">Latest articles</h2>
				</div>
				<div class="card-body p-4">
					@if($latestArticles->isEmpty())
						<p class="text-muted small mb-0">No published articles yet.</p>
					@else
						<ul class="list-unstyled mb-0 small">
							@foreach($latestArticles as $article)
								<li class="mb-2">
									<a href="{{ route('articles.show', $article->slug) }}" class="text-decoration-none">{{ $article->title }}</a>
								</li>
							@endforeach
						</ul>
					@endif
				</div>
			</div>

			<div class="card border-0 shadow-sm">
				<div class="card-body p-4">
					<form method="post" action="{{ route('logout') }}" class="d-grid gap-2">
						@csrf
						<button type="submit" class="btn btn-outline-danger btn-sm">Sign out</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
