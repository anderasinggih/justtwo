@props([
    'level' => 1,
    'size' => '2xl',
])

@php
    $sizes = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
        '2xl' => 'text-2xl',
        '3xl' => 'text-3xl',
        '4xl' => 'text-4xl leading-tight',
    ];

    $classes = "font-medium text-[var(--text-primary)] tracking-tight lowercase first-letter:uppercase " . $sizes[$size];

@endphp

@if($level == 1)
    <h1 {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</h1>
@elseif($level == 2)
    <h2 {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</h2>
@elseif($level == 3)
    <h3 {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</h3>
@else
    <h4 {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</h4>
@endif
