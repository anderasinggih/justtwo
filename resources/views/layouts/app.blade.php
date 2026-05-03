<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="{{ Auth::user()->relationship?->theme ?? 'light' }}">

<head>
    <meta charset="utf-8">

    <!-- PWA Meta Tags (Must be at the top) -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GalleryTwo">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="{{ asset('images/auth-bg.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    @livewireStyles

</head>

<body class="font-sans antialiased transition-colors duration-300"
    style="background-color: var(--bg-primary); color: var(--text-primary);" x-data="{ 
            theme: '{{ Auth::user()->relationship?->theme ?? 'light' }}',
            setTheme(newTheme) {
                this.theme = newTheme;
                document.documentElement.setAttribute('data-theme', newTheme);
            }
          }" x-init="setTheme(theme)" @theme-updated.window="setTheme($event.detail.theme)">

    <div class="min-h-screen pb-24">
        {{-- Navigation --}}
        <livewire:layout.navigation />

        {{-- Main Content --}}
        <main class="page-reveal">
            {{ $slot }}
        </main>

    </div>

    {{-- Global Lightbox --}}
    <div x-data="{ 
                show: false, 
                src: '',
                open(src) {
                    this.src = src;
                    this.show = true;
                }
             }" @open-lightbox.window="open($event.detail)" x-show="show" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md p-4 md:p-10"
        @click="show = false" @keydown.escape.window="show = false">

        <button class="absolute top-8 right-8 text-white/50 hover:text-white transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <img :src="src" class="max-w-full max-h-full rounded-2xl shadow-2xl object-contain">
    </div>


    @livewireScripts
    <script>
        // Prevent Global Pinch-to-Zoom (Safari Fix)
        document.addEventListener('touchmove', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, { passive: false });

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
                        // Support Livewire Navigate
                        if (curnode.hasAttribute('wire:navigate')) {
                            // Let Livewire handle it, but if it fails to stay in standalone, 
                            // we fall back to window.location
                            return;
                        }
                        window.location = curnode.href;
                    }
                }, false);
            }
        })(document, window.navigator, 'standalone');
    </script>
    {{-- Global Custom Confirmation Modal --}}
    <div x-data="{ 
            show: false, 
            title: '', 
            message: '', 
            confirmCallback: null,
            trigger(data) {
                this.title = data.title || 'Are you sure?';
                this.message = data.message || '';
                this.confirmCallback = data.onConfirm;
                this.show = true;
            },
            confirm() {
                if (this.confirmCallback) this.confirmCallback();
                this.show = false;
            }
         }"
         @confirm.window="trigger($event.detail)"
         class="relative z-[9999]"
         x-show="show"
         x-cloak>
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
             x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"></div>

        {{-- Modal Content --}}
        <div class="fixed inset-0 z-10 flex items-center justify-center p-6 text-center">
            <div class="w-full max-w-xs theme-card border theme-border rounded-[2.5rem] p-8 shadow-2xl space-y-6"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4">
                
                <div class="space-y-2">
                    <h3 class="text-xl font-bold theme-text lowercase tracking-tighter" x-text="title"></h3>
                    <p class="text-xs theme-text opacity-40 lowercase leading-relaxed" x-text="message"></p>
                </div>

                <div class="flex flex-col gap-2">
                    <button @click="confirm()" class="w-full py-4 theme-accent-bg text-white rounded-3xl font-bold text-sm active:scale-95 transition-all shadow-lg shadow-brand-500/20">
                        Yes, do it
                    </button>
                    <button @click="show = false" class="w-full py-4 bg-white/5 theme-text rounded-3xl font-bold text-sm active:scale-95 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>