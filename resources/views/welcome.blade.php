<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        
        <!-- PWA Meta Tags (Must be at the top) -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="GalleryTwo">
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="manifest" href="{{ asset('manifest.json') }}" type="application/manifest+json">
        <link rel="apple-touch-icon" href="{{ asset('images/auth-bg.png') }}">
        
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title>memories for two / private couple gallery</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#fdfaf6] text-gray-900 font-sans">
        <div class="relative min-h-screen overflow-hidden">
            {{-- Decorative Blobs --}}
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-100 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-romantic-rose rounded-full blur-3xl opacity-30 animate-pulse"></div>

            {{-- Nav --}}
            <nav class="relative z-10 max-w-7xl mx-auto px-6 py-8 flex justify-between items-center">
                <div class="text-2xl font-bold tracking-tighter lowercase">gallery for two.</div>
                <div class="space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium hover:text-brand-500 lowercase">dashboard</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium hover:text-brand-500 lowercase">login</a>
                        <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center rounded-full bg-brand-500 px-6 py-2 text-sm font-medium text-white hover:bg-brand-600 shadow-lg shadow-brand-200 transition-all lowercase">start your space</a>
                    @endauth
                </div>
            </nav>

            {{-- Hero --}}
            <main class="relative z-10 max-w-5xl mx-auto px-6 pt-20 pb-32 text-center">
                <x-ui.badge variant="secondary" class="mb-6 px-4 py-1">designed for couples</x-ui.badge>
                <h1 class="text-6xl md:text-8xl font-medium tracking-tighter mb-8 leading-tight lowercase">
                    every memory <br>
                    <span class="text-brand-500 italic">shared only by two.</span>
                </h1>
                <p class="text-xl text-gray-500 max-w-2xl mx-auto mb-12 lowercase leading-relaxed">
                    a beautiful, private space to preserve your photos, journals, and milestones. no public feeds, no noise. just you and your partner, forever.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <a href="{{ route('register') }}" wire:navigate class="w-full sm:w-auto rounded-full bg-brand-500 px-10 py-5 text-lg font-medium text-white hover:bg-brand-600 shadow-2xl shadow-brand-300 transition-all animate-bounce-subtle lowercase">
                        create your shared space
                    </a>
                    <a href="#features" class="w-full sm:w-auto rounded-full bg-white border border-gray-100 px-10 py-5 text-lg font-medium hover:bg-gray-50 transition-all lowercase">
                        explore features
                    </a>
                </div>

                {{-- Mockup --}}
                <div class="mt-24 relative max-w-4xl mx-auto">
                    <div class="absolute inset-0 bg-brand-200 rounded-[3rem] rotate-2 scale-105 opacity-20 blur-xl"></div>
                    <div class="relative bg-white p-4 rounded-[4rem] shadow-2xl border border-gray-100/50">
                        <div class="bg-gray-50 rounded-[3.5rem] aspect-[16/9] flex items-center justify-center overflow-hidden">
                             <div class="text-center p-12">
                                <p class="text-gray-300 lowercase italic">dashboard preview coming soon</p>
                             </div>
                        </div>
                    </div>
                </div>
            </main>

            {{-- Features --}}
            <section id="features" class="relative z-10 max-w-7xl mx-auto px-6 py-24">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <div class="space-y-4">
                        <div class="w-16 h-16 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-medium tracking-tight lowercase">private gallery</h3>
                        <p class="text-gray-500 lowercase">store all your photos in a beautiful masonry grid. only for your eyes.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="w-16 h-16 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h3 class="text-2xl font-medium tracking-tight lowercase">shared journal</h3>
                        <p class="text-gray-500 lowercase">write letters, share thoughts, and record your moods together.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="w-16 h-16 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-medium tracking-tight lowercase">milestones</h3>
                        <p class="text-gray-500 lowercase">track every significant date and count down to upcoming celebrations.</p>
                    </div>
                </div>
            </section>

            <footer class="relative z-10 max-w-7xl mx-auto px-6 py-20 text-center border-t border-gray-100">
                <p class="text-gray-400 text-sm lowercase">&copy; {{ date('Y') }} gallery for two. made with love for couples.</p>
            </footer>
        </div>

        <style>
            @keyframes bounce-subtle {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            .animate-bounce-subtle {
                animation: bounce-subtle 3s ease-in-out infinite;
            }
        </style>
    </body>
</html>
