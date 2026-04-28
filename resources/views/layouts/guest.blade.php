<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- PWA Meta Tags -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="GalleryTwo">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/auth-bg.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
        @livewireStyles

    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#fdfaf6]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md mt-6 px-10 py-12 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden rounded-[2.5rem] border border-gray-100">
                <div class="flex justify-center mb-10">
                    <a href="/" wire:navigate>
                        <x-application-logo class="w-20 h-20 fill-current text-brand-500" />
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
        @livewireScripts

        <script>
            // Mencegah link membuka Safari browser (tetap di dalam Web App)
            document.addEventListener('click', function(event) {
                var element = event.target;
                while (element && element.tagName !== 'A') {
                    element = element.parentNode;
                }

                if (element && element.tagName === 'A' && element.hasAttribute('href')) {
                    var href = element.getAttribute('href');
                    // Jika link internal (berawal dengan / atau mengandung hostname), cegah perilaku default
                    if (href.startsWith('/') || href.includes(window.location.hostname)) {
                        // Jangan cegah jika itu adalah link Livewire navigate (biarkan Livewire yang handle)
                        if (element.hasAttribute('wire:navigate')) return;
                        
                        event.preventDefault();
                        window.location.href = href;
                    }
                }
            }, false);
        </script>
    </body>
</html>
