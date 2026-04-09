@if ($paginator->hasPages())
	<nav
		role="navigation"
		aria-label="Pagination"
		class="mt-6 flex flex-col gap-3 border-t border-zinc-100 pt-4 sm:flex-row sm:items-center sm:justify-between"
	>
		<p class="text-sm text-zinc-500">
			Showing
			<span class="font-medium text-zinc-800">{{ $paginator->firstItem() }}</span>
			–
			<span class="font-medium text-zinc-800">{{ $paginator->lastItem() }}</span>
			of
			<span class="font-medium text-zinc-800">{{ $paginator->total() }}</span>
		</p>
		<div class="flex flex-wrap items-center gap-1">
			@if ($paginator->onFirstPage())
				<span
					class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 px-2 text-sm text-zinc-400"
				>Prev</span>
			@else
				<a
					href="{{ $paginator->previousPageUrl() }}"
					class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white px-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50"
				>Prev</a>
			@endif

			@foreach ($elements as $element)
				@if (is_string($element))
					<span
						class="inline-flex min-h-9 min-w-9 items-center justify-center px-1 text-sm text-zinc-400"
					>…</span>
				@endif
				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<span
								class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg bg-indigo-600 px-3 text-sm font-semibold text-white shadow-sm"
							>{{ $page }}</span>
						@else
							<a
								href="{{ $url }}"
								class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50"
							>{{ $page }}</a>
						@endif
					@endforeach
				@endif
			@endforeach

			@if ($paginator->hasMorePages())
				<a
					href="{{ $paginator->nextPageUrl() }}"
					class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white px-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50"
				>Next</a>
			@else
				<span
					class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 px-2 text-sm text-zinc-400"
				>Next</span>
			@endif
		</div>
	</nav>
@endif
