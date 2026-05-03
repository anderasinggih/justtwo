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

        // Global Swipe Navigation
        (function() {
            let touchstartX = 0;
            let touchstartY = 0;
            let touchendX = 0;
            let touchendY = 0;

            const routes = ['/dashboard', '/planner', '/memory/new', '/gallery', '/profile'];

            function handleGesture(event) {
                const deltax = touchendX - touchstartX;
                const deltay = touchendY - touchstartY;

                // Threshold 80px, must be horizontal
                if (Math.abs(deltax) > Math.abs(deltay) && Math.abs(deltax) > 80) {
                    // Ignore swipes on horizontal scroll areas or interactive elements
                    if (event.target.closest('.overflow-x-auto, .snap-x, .pinch-zoom-container, input, textarea, button')) return;

                    const currentPath = window.location.pathname;
                    let currentIndex = routes.findIndex(r => currentPath.startsWith(r));
                    
                    if (currentIndex === -1 && currentPath === '/') currentIndex = 0;
                    if (currentIndex === -1) return;

                    if (deltax < 0) {
                        // Swipe Left -> Next Menu
                        if (currentIndex < routes.length - 1) {
                            Livewire.navigate(routes[currentIndex + 1]);
                        }
                    } else {
                        // Swipe Right -> Prev Menu
                        if (currentIndex > 0) {
                            Livewire.navigate(routes[currentIndex - 1]);
                        }
                    }
                }
            }

            document.addEventListener('touchstart', e => {
                touchstartX = e.changedTouches[0].screenX;
                touchstartY = e.changedTouches[0].screenY;
            }, {passive: true});

            document.addEventListener('touchend', e => {
                touchendX = e.changedTouches[0].screenX;
                touchendY = e.changedTouches[0].screenY;
                handleGesture(e);
            }, {passive: true});
        })();
    </script>
</body>

</html>