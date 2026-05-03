@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
	<x-admin.page-header title="Dashboard">
		<x-slot:subtitle>Overview of your content and inbox</x-slot:subtitle>
	</x-admin.page-header>

	@if (! empty($overview))
		<div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<x-admin.card><p class="text-sm font-medium text-zinc-500">Leads</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['leads_total'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">New leads</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['leads_new'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">Services</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['services'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">Case studies</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['case_studies'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">Testimonials</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['testimonials'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">FAQs</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['faqs'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">Users</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['users'] }}</p></x-admin.card>
			<x-admin.card><p class="text-sm font-medium text-zinc-500">Articles (published / draft)</p><p class="mt-2 text-3xl font-semibold text-zinc-900">{{ $overview['articles_published'] }} / {{ $overview['articles_draft'] }}</p></x-admin.card>
		</div>
	@endif

	<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft articles</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftArticles }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Pending review</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $articlesPendingReview }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Scheduled articles</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $articlesScheduled }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Published articles</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $articlesPublished }}</p>
		</x-admin.card>
	</div>

	<div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Articles total</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $articlesTotal }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">AI drafts today</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $articlesAiToday }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Generation issues today</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $articleGenerationIssuesToday }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Unread messages</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $unreadMessages }}</p>
		</x-admin.card>
	</div>

	<div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3 lg:grid-cols-6">
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft case studies</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftCaseStudies }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft market updates</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftMarketUpdates }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Case studies pending review</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $caseStudiesPendingReview }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Market updates pending review</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $marketUpdatesPendingReview }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">AI case studies (week)</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $caseStudiesAiWeek }}</p>
		</x-admin.card>
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">AI market updates (week)</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $marketUpdatesAiWeek }}</p>
		</x-admin.card>
	</div>

	<div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
		<x-admin.card>
			<p class="text-sm font-medium text-zinc-500">Draft services</p>
			<p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900">{{ $draftServices }}</p>
		</x-admin.card>
		@if ($lastContentGenerationLog)
			<x-admin.card>
				<p class="text-sm font-medium text-zinc-500">Last AI generation log</p>
				<p class="mt-2 text-sm text-zinc-700">
					{{ $lastContentGenerationLog->log_date?->toDateString() }}
					— {{ $lastContentGenerationLog->status }}
					@if ($lastContentGenerationLog->content_type)
						<span class="text-zinc-500">({{ $lastContentGenerationLog->content_type }})</span>
					@endif
				</p>
				@if ($lastContentGenerationLog->error_message)
					<p class="mt-2 text-xs text-amber-700">{{ \Illuminate\Support\Str::limit($lastContentGenerationLog->error_message, 160) }}</p>
				@endif
			</x-admin.card>
		@endif
	</div>

	<div class="mt-8 space-y-4">
		<h2 class="text-lg font-semibold text-zinc-900">Ali 1.0 quick generate</h2>
		<p class="text-sm text-zinc-600">
			Scheduler runs <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs">content:generate-ai</code> daily. Case studies and market updates honor interval env vars; use manual generation when you need a draft immediately.
			On DigitalOcean App Platform, schedule at least one worker invocation per day for <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs">php artisan schedule:run</code> — sub‑hourly resolution is not guaranteed.
		</p>
		<div class="grid gap-4 lg:grid-cols-3">
			@can('ai_articles.generate')
				<x-admin.card>
					<h3 class="text-sm font-semibold text-zinc-900">Article</h3>
					<form method="post" action="{{ route('admin.ai-content.quick.article') }}" class="mt-4 space-y-3">
						@csrf
						<x-admin.select name="article_type" label="Bucket">
							@foreach (['latest_discovery', 'today_trends', 'latest_tech', 'company_update', 'tutorial', 'insight'] as $t)
								<option value="{{ $t }}">{{ $t }}</option>
							@endforeach
						</x-admin.select>
						<x-admin.input name="topic" label="Topic (optional)" />
						<x-admin.button variant="primary" type="submit" class="w-full">Generate draft</x-admin.button>
					</form>
				</x-admin.card>
			@endcan
			@can('ai_case_studies.generate')
				<x-admin.card>
					<h3 class="text-sm font-semibold text-zinc-900">Concept case study</h3>
					<form method="post" action="{{ route('admin.ai-content.quick.case-study') }}" class="mt-4 space-y-3">
						@csrf
						<x-admin.input name="topic" label="Topic hint (optional)" />
						<x-admin.button variant="primary" type="submit" class="w-full">Generate draft</x-admin.button>
					</form>
				</x-admin.card>
			@endcan
			@can('ai_market_updates.generate')
				<x-admin.card>
					<h3 class="text-sm font-semibold text-zinc-900">Market update</h3>
					<form method="post" action="{{ route('admin.ai-content.quick.market-update') }}" class="mt-4 space-y-3">
						@csrf
						<x-admin.input name="topic" label="Market area hint (optional)" />
						<x-admin.button variant="primary" type="submit" class="w-full">Generate draft</x-admin.button>
					</form>
				</x-admin.card>
			@endcan
		</div>
	</div>
@endsection
