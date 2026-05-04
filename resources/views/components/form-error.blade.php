@props([
	'name',
	'class' => 'mt-1 text-sm text-red-600',
])
<p {{ $attributes->merge(['class' => $class, 'data-error-for' => $name, 'role' => 'alert']) }}></p>
