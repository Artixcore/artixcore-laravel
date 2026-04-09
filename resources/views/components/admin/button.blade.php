@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])
@php
    $variants = [
        'primary' =>
            'bg-indigo-600 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
        'secondary' =>
            'border border-zinc-200 bg-white text-zinc-700 shadow-sm hover:bg-zinc-50',
        'ghost' => 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900',
        'danger' => 'border border-red-200 bg-white text-red-600 shadow-sm hover:bg-red-50',
    ];
    $cls =
        'inline-flex items-center justify-center gap-2 rounded-[10px] px-3.5 py-2 text-sm font-medium transition ' .
        ($variants[$variant] ?? $variants['primary']);
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif
