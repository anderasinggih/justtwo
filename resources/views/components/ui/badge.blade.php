@props([
    'variant' => 'primary',
])

@php
    $baseStyles = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2';
    
    $variants = [
        'primary' => 'bg-brand-100 text-brand-700',
        'secondary' => 'bg-romantic-rose text-brand-600',
        'outline' => 'text-gray-600 border border-gray-200',
        'success' => 'bg-emerald-100 text-emerald-700',
    ];

    $classes = "{$baseStyles} {$variants[$variant]}";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
