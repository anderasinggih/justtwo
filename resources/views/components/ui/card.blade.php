@props([
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => "bg-[var(--card-bg)] border border-gray-100/10 rounded-[2rem] shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden " . $padding]) }} style="color: var(--text-primary)">
    {{ $slot }}
</div>

