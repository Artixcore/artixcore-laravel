@props(['href' => null, 'danger' => false])
@php
    $base =
        'block w-full px-3 py-2 text-left text-sm font-medium transition-colors ' .
        ($danger
            ? 'text-red-600 hover:bg-red-50'
            : 'text-zinc-700 hover:bg-zinc-50');
@endphp
@if($href)
    <a href="{{ $href }}" role="menuitem" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</a>
@else
    <button type="button" role="menuitem" {{ $attributes->merge(['class' => $base . ' w-full']) }}>{{ $slot }}</button>
@endif
