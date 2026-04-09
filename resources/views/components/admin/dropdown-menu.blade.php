@props(['align' => 'end'])
@php
    $alignClass = $align === 'start' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
@endphp
<details class="relative inline-block text-left">
    <summary
        class="flex cursor-pointer list-none items-center justify-center rounded-lg p-1.5 text-zinc-500 outline-none ring-indigo-500/30 transition hover:bg-zinc-100 hover:text-zinc-800 focus-visible:ring-2 [&::-webkit-details-marker]:hidden"
        aria-label="Actions"
    >
        <x-admin.icon name="ellipsis-vertical" class="size-5 text-zinc-500" />
    </summary>
    <div
        class="absolute {{ $alignClass }} z-50 mt-1 min-w-[10rem] rounded-xl border border-zinc-200/90 bg-white py-1 shadow-lg shadow-zinc-900/5 ring-1 ring-zinc-900/5"
        role="menu"
    >
        {{ $slot }}
    </div>
</details>
