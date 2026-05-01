<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    
    @php
        $settings = \App\Models\PublicSetting::getSettings();
        $relationship = \App\Models\Relationship::orderBy('id', 'desc')->first();
        $spaceName = $relationship?->name ?? 'justtwo';
    @endphp

    <title>{{ $spaceName }} — {{ $settings->journey_title ?? 'Our Journey' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @livewireStyles
</head>

<body x-data="{
        currentTheme: '{{ $settings->theme ?? 'light' }}',
        themes: ['light', 'dark', 'rose', 'midnight', 'sky', 'mint', 'lavender', 'pink'],
        init() {
            if (this.currentTheme === 'mix') {
                this.currentTheme = this.themes[Math.floor(Math.random() * this.themes.length)];
                setInterval(() => {
                    let idx = this.themes.indexOf(this.currentTheme);
                    this.currentTheme = this.themes[(idx + 1) % this.themes.length];
                }, 300000);
            }
        }
    }" 
    :data-theme="currentTheme"
    :class="currentTheme"
    class="antialiased font-sans theme-text theme-bg transition-colors duration-1000">
    
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden opacity-20">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-brand-500/20 blur-[120px] rounded-full animate-float"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-brand-500/10 blur-[120px] rounded-full animate-float-reverse"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
