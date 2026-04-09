@php
    $user = auth()->user();
    $initials = $user
        ? strtoupper(
            collect(preg_split('/\s+/', $user->name ?? $user->email, -1, PREG_SPLIT_NO_EMPTY))
                ->take(2)
                ->map(fn ($p) => mb_substr($p, 0, 1))
                ->implode(''),
        )
        : '?';
@endphp
<header
    class="sticky top-0 z-30 flex h-14 shrink-0 items-center gap-3 border-b border-zinc-200/80 bg-white/90 px-4 shadow-sm shadow-zinc-900/[0.03] backdrop-blur-md sm:gap-4 sm:px-6"
>
    <button
        type="button"
        id="admin-sidebar-toggle-mobile"
        class="inline-flex rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 md:hidden"
        aria-label="Open menu"
    >
        <x-admin.icon name="bars-3" class="size-5 text-zinc-600" />
    </button>
    <button
        type="button"
        id="admin-sidebar-toggle-desktop"
        class="hidden rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 md:inline-flex"
        aria-label="Collapse sidebar"
    >
        <x-admin.icon name="chevron-left" class="size-5 text-zinc-600 transition-transform duration-200" />
    </button>

    <div class="relative min-w-0 flex-1 max-w-xl">
        <x-admin.icon
            name="magnifying-glass"
            class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400"
        />
        <input
            type="search"
            name="admin_search"
            class="w-full rounded-[10px] border border-zinc-200 bg-zinc-50/80 py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            placeholder="Search…"
            autocomplete="off"
            disabled
            aria-disabled="true"
            title="Search coming soon"
        />
    </div>

    <div class="flex shrink-0 items-center gap-1 sm:gap-2">
        <button
            type="button"
            class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700"
            aria-label="Notifications"
            disabled
            title="No new notifications"
        >
            <x-admin.icon name="bell" class="size-5" />
        </button>

        <details class="relative">
            <summary
                class="flex cursor-pointer list-none items-center gap-2 rounded-[10px] p-1.5 pr-2 hover:bg-zinc-100 [&::-webkit-details-marker]:hidden"
            >
                <span
                    class="flex size-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700"
                >{{ $initials }}</span>
                <span class="hidden max-w-[10rem] truncate text-sm font-medium text-zinc-800 sm:inline">{{ $user?->name ?? $user?->email }}</span>
                <x-admin.icon name="chevron-right" class="hidden size-4 text-zinc-400 sm:block sm:rotate-90" />
            </summary>
            <div
                class="absolute right-0 z-50 mt-1 w-52 rounded-xl border border-zinc-200/90 bg-white py-1 shadow-lg shadow-zinc-900/5 ring-1 ring-zinc-900/5"
            >
                <div class="border-b border-zinc-100 px-3 py-2">
                    <p class="truncate text-sm font-medium text-zinc-900">{{ $user?->name ?? 'Account' }}</p>
                    <p class="truncate text-xs text-zinc-500">{{ $user?->email }}</p>
                </div>
                <a
                    href="{{ url('/') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50"
                >
                    <x-admin.icon name="arrow-top-right-on-square" class="size-4 text-zinc-400" />
                    View site
                </a>
                <form method="post" action="{{ route('admin.logout') }}" class="border-t border-zinc-100">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                    >
                        <x-admin.icon name="arrow-right-on-rectangle" class="size-4" />
                        Sign out
                    </button>
                </form>
            </div>
        </details>
    </div>
</header>
