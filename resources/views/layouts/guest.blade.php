<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <!-- PWA Meta Tags (Must be at the top) -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GalleryTwo">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json" crossorigin="use-credentials">
    <link rel="apple-touch-icon" href="{{ asset('images/auth-bg.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

</head>

@php
    $settings = \App\Models\PublicSetting::getSettings();
    $theme = $settings->theme ?? 'light';
    
    // Handle "mix" mode for auth pages by picking a random theme
    if ($theme === 'mix') {
        $themes = ['light', 'dark', 'rose', 'midnight', 'sky', 'mint', 'lavender', 'pink'];
        $theme = $themes[array_rand($themes)];
    }
@endphp

<body class="font-sans antialiased theme-bg theme-text transition-colors duration-1000" data-theme="{{ $theme }}">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-0 sm:pt-0">
        <div
            class="w-full sm:max-w-md sm:mt-6 px-6 py-12 sm:px-10 sm:py-12 sm:theme-card sm:shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden sm:rounded-[2.5rem] sm:border theme-border">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(err => console.error('SW Error', err));
        }

        // PWA Standalone Navigation Fix for iOS
        (function(document, navigator, standalone) {
            if ((standalone in navigator) && navigator[standalone]) {
                var curnode, location = document.location, stop = /^(a|html)$/i;
                document.addEventListener('click', function(e) {
                    curnode = e.target;
                    while (!(stop).test(curnode.nodeName)) {
                        curnode = curnode.parentNode;
                    }
                    if ('href' in curnode && (curnode.href.indexOf('http') || ~curnode.href.indexOf(location.host)) && (!curnode.classList.contains('no-pwa-fix'))) {
                        e.preventDefault();
                        if (curnode.hasAttribute('wire:navigate')) {
                            return;
                        }
                        window.location = curnode.href;
                    }
                }, false);
            }
        })(document, window.navigator, 'standalone');
    </script>
</body>

</html>