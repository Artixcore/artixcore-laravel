@props(['value'])
@php
    $v = is_string($value) ? strtolower(trim($value)) : '';
    $map = [
        'published' => ['label' => 'Published', 'class' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/15'],
        'draft' => ['label' => 'Draft', 'class' => 'bg-violet-50 text-violet-800 ring-violet-600/15'],
        'active' => ['label' => 'Active', 'class' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/15'],
        'inactive' => ['label' => 'Inactive', 'class' => 'bg-zinc-100 text-zinc-700 ring-zinc-500/10'],
        'yes' => ['label' => 'Yes', 'class' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/15'],
        'no' => ['label' => 'No', 'class' => 'bg-orange-50 text-orange-800 ring-orange-600/15'],
        'read' => ['label' => 'Read', 'class' => 'bg-zinc-100 text-zinc-600 ring-zinc-500/10'],
        'unread' => ['label' => 'Unread', 'class' => 'bg-indigo-50 text-indigo-800 ring-indigo-600/15'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-50 text-red-800 ring-red-600/15'],
        'canceled' => ['label' => 'Cancelled', 'class' => 'bg-red-50 text-red-800 ring-red-600/15'],
        'pending' => ['label' => 'Pending', 'class' => 'bg-violet-50 text-violet-800 ring-violet-600/15'],
        'completed' => ['label' => 'Completed', 'class' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/15'],
    ];
    $cfg = $map[$v] ?? ['label' => (string) $value, 'class' => 'bg-zinc-100 text-zinc-700 ring-zinc-500/10'];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . $cfg['class']]) }}>
    {{ $cfg['label'] }}
</span>
