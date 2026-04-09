@props([
    'href',
    'icon',
    'active' => false,
])
@php
    $active = filter_var($active, FILTER_VALIDATE_BOOLEAN);
@endphp
<li class="list-none">
    <a
        href="{{ $href }}"
        {{ $attributes->merge([
            'class' =>
                'admin-sidebar-item group flex items-center gap-3 rounded-[10px] px-3 py-2 text-sm font-medium transition-colors ' .
                ($active
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900'),
        ]) }}
    >
        <x-admin.icon
            :name="$icon"
            class="size-[1.125rem] shrink-0 {{ $active ? 'text-indigo-600' : 'text-zinc-400 group-hover:text-zinc-500' }}"
        />
        <span class="admin-sidebar-label truncate">{{ $slot }}</span>
    </a>
</li>
