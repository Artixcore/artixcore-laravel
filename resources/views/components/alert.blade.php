@props([
    'type' => 'info',
    'dismissible' => true,
])
@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'error' => 'border-red-200 bg-red-50 text-red-900',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
        'info' => 'border-sky-200 bg-sky-50 text-sky-900',
    ];
    $cls = $styles[$type] ?? $styles['info'];
@endphp
<div {{ $attributes->merge(['class' => 'rounded-lg border px-4 py-3 text-sm '.$cls, 'role' => 'alert']) }}>
	<div class="flex items-start gap-2">
		<div class="min-w-0 flex-1">{{ $slot }}</div>
		@if ($dismissible)
			<button type="button" class="shrink-0 rounded p-1 opacity-70 hover:opacity-100" onclick="this.closest('[role=alert]').remove()" aria-label="Dismiss">&times;</button>
		@endif
	</div>
</div>
