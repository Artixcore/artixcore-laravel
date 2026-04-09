@props([
    'label' => null,
    'name',
    'checked' => false,
])
@php $id = $attributes->get('id') ?? $name; @endphp
<label
    class="inline-flex cursor-pointer items-center gap-3 has-[:checked]:[&_.admin-toggle-knob]:translate-x-[1.25rem] has-[:checked]:[&_.admin-toggle-track]:bg-indigo-600"
>
    <input type="hidden" name="{{ $name }}" value="0" />
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $id }}"
        value="1"
        @checked($checked)
        {{ $attributes->except('id')->merge([
            'class' => 'peer sr-only',
        ]) }}
    />
    <span
        class="admin-toggle-track relative inline-block h-6 w-11 shrink-0 rounded-full bg-zinc-200 transition peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500/40"
        aria-hidden="true"
    >
        <span
            class="admin-toggle-knob absolute left-0.5 top-0.5 block size-5 rounded-full bg-white shadow-sm transition-transform"
        ></span>
    </span>
    @if($label)
        <span class="text-sm font-medium text-zinc-700">{{ $label }}</span>
    @endif
</label>
