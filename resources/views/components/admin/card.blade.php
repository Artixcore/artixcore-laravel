@props(['noPadding' => false])
<div
    {{ $attributes->merge([
        'class' =>
            'rounded-xl border border-zinc-200/80 bg-white shadow-[var(--shadow-admin)]' .
            ($noPadding ? '' : ' p-5 sm:p-6'),
    ]) }}
>
    {{ $slot }}
</div>
