@props([
    'label' => null,
    'name',
    'rows' => 3,
])
@php
    $id = $attributes->get('id', $name);
@endphp
<div>
    @if ($label)
        <label for="{{ $id }}" class="admin-field-label">{{ $label }}</label>
    @endif
    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'id' => $id,
            'class' => 'admin-field-input',
        ]) }}
    >{{ $slot }}</textarea>
</div>
