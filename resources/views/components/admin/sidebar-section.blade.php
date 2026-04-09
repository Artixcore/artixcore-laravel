@props(['title'])
<div {{ $attributes->merge(['class' => 'mb-1']) }}>
    <p class="admin-sidebar-section-title mb-2 px-3 text-[11px] font-semibold uppercase tracking-wide text-zinc-400">{{ $title }}</p>
    <ul class="flex flex-col gap-0.5">{{ $slot }}</ul>
</div>
