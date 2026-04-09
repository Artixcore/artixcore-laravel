@props(['title'])
<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="text-xl font-semibold tracking-tight text-zinc-900 sm:text-2xl">{{ $title }}</h1>
        @if (isset($subtitle) && !$subtitle->isEmpty())
            <p class="mt-1 text-sm text-zinc-500">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex flex-shrink-0 flex-col gap-3 sm:flex-row sm:items-center">
        @isset($filters)
            @if($filters->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2">{{ $filters }}</div>
            @endif
        @endisset
        @isset($actions)
            @if($actions->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endif
        @endisset
    </div>
</div>
