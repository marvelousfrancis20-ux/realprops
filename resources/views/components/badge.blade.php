@props(['badge' => 'gray', 'label' => null])

@php
    $map = [
        'gray' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'red' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        'teal' => 'bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300',
        'blue' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
    ];

    $class = $map[$badge] ?? $map['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold $class"]) }}>
    {{ $label ?? $slot }}
</span>
