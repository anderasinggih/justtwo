@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $baseStyles = 'inline-flex items-center justify-center rounded-full font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none active:scale-95';
    
    $variants = [
        'primary' => 'bg-brand-500 text-white hover:bg-brand-600 focus:ring-brand-500 shadow-sm shadow-brand-200',
        'secondary' => 'bg-romantic-rose text-brand-600 hover:bg-brand-100 focus:ring-brand-200',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 hover:text-gray-900',
        'outline' => 'bg-transparent border border-gray-200 text-gray-700 hover:bg-gray-50',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-5 py-2.5 text-base',
        'lg' => 'px-7 py-3 text-lg',
    ];

    $classes = "{$baseStyles} {$variants[$variant]} {$sizes[$size]}";
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
