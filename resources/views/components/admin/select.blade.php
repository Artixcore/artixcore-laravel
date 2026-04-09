@props([
    'label' => null,
    'name',
])
@php
    $id = $attributes->get('id', $name);
@endphp
<div>
    @if($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-zinc-700">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        {{ $attributes->merge([
            'id' => $id,
            'class' =>
                'block w-full rounded-[10px] border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20',
        ]) }}
    >
        {{ $slot }}
    </select>
</div>
