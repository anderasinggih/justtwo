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

    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    @php
        $settings = \App\Models\PublicSetting::getSettings();
        $banners = $settings->banner_paths ?? [];
        $bannerData = $settings->banner_data ?? [];
        $spaceName = \App\Models\Relationship::orderBy('id', 'desc')->first()?->name ?? 'justtwo';
    @endphp

    <title>{{ $settings->hero_title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased font-sans theme-text theme-bg transition-colors duration-500"
    data-theme="{{ $settings->theme ?? 'light' }}">
    <div class="relative min-h-screen overflow-hidden">
        {{-- Decorative Atmospheric Glows --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-[1]">
            <div
                class="absolute top-[20%] left-[20%] w-96 h-96 bg-brand-400 rounded-full blur-[100px] animate-float animate-soft-pulse">
            </div>
            <div
                class="absolute top-[40%] right-[15%] w-72 h-72 bg-brand-500 rounded-full blur-[80px] animate-float-reverse opacity-20">
            </div>
            <div
                class="absolute bottom-[20%] left-[30%] w-80 h-80 bg-brand-300 rounded-full blur-[90px] animate-float opacity-15">
            </div>
        </div>

        {{-- Nav --}}
        <nav
            class="relative z-10 max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-8 flex justify-between items-center mix-blend-difference text-white">
            <div class="text-xl font-bold tracking-tighter lowercase">{{ $spaceName }}</div>
            <div class="space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="text-sm font-medium hover:text-brand-200 transition-colors lowercase">dashboard</a>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                        class="text-sm font-medium hover:text-brand-200 transition-colors lowercase">login</a>
                @endauth
            </div>
        </nav>

        {{-- Hero Section with Background Carousel --}}
        <main
            class="relative h-[35vh] md:h-[80vh] w-full flex flex-col items-center justify-center text-center overflow-hidden"
            x-data="{ 
                active: 0,
                banners: {{ json_encode($banners) }},
                bannerData: {{ json_encode($bannerData) }},
                timer: null,
                showTitle: true,
                titleTimer: null,
                isVideo(path) {
                    if (!path) return false;
                    const ext = path.split('.').pop().toLowerCase();
                    return ['mp4', 'webm', 'mov', 'ogg'].includes(ext);
                },
                next() { 
                    this.active = (this.active + 1) % this.banners.length;
                    this.startRotation();
                },
                startRotation() {
                    clearTimeout(this.timer);
                    clearTimeout(this.titleTimer);
                    this.showTitle = true;
                    
                    if (this.banners.length <= 1 && !this.isVideo(this.banners[this.active])) return;
                    
                    const currentPath = this.banners[this.active];
                    if (this.isVideo(currentPath)) {
                        // Hide title after 4 seconds for videos
                        this.titleTimer = setTimeout(() => {
                            this.showTitle = false;
                        }, 4000);
                    } else {
                        // Regular rotation for images
                        this.timer = setTimeout(() => this.next(), 5000);
                    }
                },
                init() { 
                    this.startRotation();
                }
            }">
            {{-- Banners Carousel as Background --}}
            <div class="absolute inset-0 z-0 h-full w-full">
                @forelse($banners as $index => $banner)
                    <div x-show="active === {{ $index }}" x-transition:enter="transition opacity duration-1000 ease-in-out"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition opacity duration-1000 ease-in-out"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="absolute inset-0 w-full h-full">
                        @php
                            $extension = pathinfo($banner, PATHINFO_EXTENSION);
                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg', 'mov']);
                        @endphp

                        @if ($isVideo)
                            <video src="{{ Storage::disk('public')->url($banner) }}" class="w-full h-full object-cover" autoplay
                                muted playsinline @ended="next()"></video>
                        @else
                            <img src="{{ Storage::disk('public')->url($banner) }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                @empty
                    <div class="absolute inset-0 bg-[#fdfaf6]"></div>
                @endforelse
            </div>

            {{-- Content Overlay --}}
            <div class="relative z-10 w-full px-3 md:px-12 lg:px-24 pt-24 md:pt-0 max-h-full overflow-hidden" x-data="{ 
                    fullText: '',
                    index: 0,
                    typingInterval: null,
                    isErasing: false,
                    targetText: '',
                    type() {
                        if (this.isErasing) {
                            if (this.index > 0) {
                                let prevSpace = this.fullText.lastIndexOf(' ', this.index - 1);
                                let prevNewline = this.fullText.lastIndexOf('\n', this.index - 1);
                                let nextIdx = Math.max(prevSpace, prevNewline);
                                this.index = nextIdx === -1 ? 0 : nextIdx;
                                this.typingInterval = setTimeout(() => this.type(), 100);
                            } else {
                                this.isErasing = false;
                                this.fullText = this.targetText;
                                this.type();
                            }
                        } else {
                            if (this.index < this.fullText.length) {
                                let nextSpace = this.fullText.indexOf(' ', this.index + 1);
                                let nextNewline = this.fullText.indexOf('\n', this.index + 1);
                                let nextIndex = (nextSpace !== -1 && nextNewline !== -1) ? Math.min(nextSpace, nextNewline) : 
                                                (nextSpace !== -1 ? nextSpace : (nextNewline !== -1 ? nextNewline : this.fullText.length));
                                this.index = nextIndex;
                                this.typingInterval = setTimeout(() => this.type(), 200);
                            }
                        }
                    },
                    startAnimation(newText) {
                        clearTimeout(this.typingInterval);
                        this.targetText = newText || '';
                        if (this.fullText && this.index > 0) {
                            this.isErasing = true;
                            this.type();
                        } else {
                            this.isErasing = false;
                            this.fullText = this.targetText;
                            this.index = 0;
                            this.type();
                        }
                    }
                 }" x-init="$watch('active', val => {
                    let currentBanner = bannerData[val];
                    startAnimation(currentBanner ? currentBanner.title : '');
                 }); 
                 startAnimation(bannerData[active] ? bannerData[active].title : '');">
                @foreach($bannerData as $index => $data)
                    <div x-show="active === {{ $index }} && showTitle" 
                         x-transition:enter="transition opacity duration-1000"
                         x-transition:leave="transition opacity duration-1000"
                         class="space-y-4">
                        <h1 class="text-sm md:text-3xl font-medium tracking-tight text-white drop-shadow-2xl text-justify"
                            style="text-align-last: justify;">
                            <template x-for="(line, i) in fullText.substring(0, index).split('\n')" :key="i">
                                <div x-text="line" class="w-full" style="text-align-last: justify;"></div>
                            </template>
                        </h1>
                    </div>
                @endforeach
                
                <div x-show="fullText && index === fullText.length && showTitle"
                     x-transition:enter="transition all duration-1000 delay-500 opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition all duration-1000 opacity-0 translate-y-4"
                     class="text-sm md:text-lg opacity-40 lowercase italic tracking-tight">
                    {{ $settings->hero_subtitle }}
                </div>
            </div>

            {{-- Media Carousel --}}
            <div class="max-w-7xl mx-auto px-4 md:px-12 mb-20">
                <div
                    class="relative aspect-[16/9] md:aspect-[21/9] rounded-[2.5rem] md:rounded-[3.5rem] overflow-hidden bg-gray-100 shadow-2xl">
                    @foreach($banners as $index => $banner)
                        @php
                            $extension = pathinfo($banner, PATHINFO_EXTENSION);
                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg', 'mov']);
                        @endphp

                        @if ($isVideo)
                            <video src="{{ Storage::disk('public')->url($banner) }}"
                                x-show="active === {{ $index }}" x-transition:enter="transition opacity duration-1000"
                                x-transition:leave="transition opacity duration-1000"
                                class="absolute inset-0 w-full h-full object-cover" autoplay muted
                                playsinline @ended="next()"></video>
                        @else
                            <img src="{{ Storage::disk('public')->url($banner) }}" x-show="active === {{ $index }}"
                                x-transition:enter="transition opacity duration-1000"
                                x-transition:leave="transition opacity duration-1000"
                                class="absolute inset-0 w-full h-full object-cover">
                        @endif
                    @endforeach

                    {{-- Navigation Dots --}}
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2">
                        @foreach($banners as $index => $banner)
                            <button @click="active = {{ $index }}"
                                class="w-1.5 h-1.5 rounded-full transition-all duration-500"
                                :class="active === {{ $index }} ? 'bg-white w-8' : 'bg-white/30'"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>

        {{-- Public Feed --}}
        <section class="relative z-10 max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-24 bg-transparent">
            <div class="text-center mb-6 md:mb-16">
                <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">public stories</h2>
            </div>
            <livewire:publicfeed />
        </section>
        <footer class="py-8 text-center border-t theme-border bg-transparent">
            <p class="text-[10px] opacity-40 uppercase tracking-tight">
                All Rights Reserved ©Copyright {{ $spaceName }}
            </p>
        </footer>
    </div>

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(150px, -200px);
            }

            66% {
                transform: translate(-120px, 150px);
            }
        }

        @keyframes float-reverse {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(-180px, 150px);
            }

            66% {
                transform: translate(120px, -180px);
            }
        }

        @keyframes soft-pulse {

            0%,
            100% {
                opacity: 0.15;
                transform: scale(1);
            }

            50% {
                opacity: 0.3;
                transform: scale(1.1);
            }
        }

        .animate-float {
            animation: float 20s ease-in-out infinite;
        }

        .animate-float-reverse {
            animation: float-reverse 25s ease-in-out infinite;
        }

        .animate-soft-pulse {
            animation: soft-pulse 10s ease-in-out infinite;
        }

        .animate-blink {
            animation: blink 0.8s step-end infinite;
        }
    </style>
    @livewireScripts
</body>

</html>