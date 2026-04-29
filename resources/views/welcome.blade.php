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
        $relationship = \App\Models\Relationship::orderBy('id', 'desc')->first();
        $spaceName = $relationship?->name ?? 'justtwo';
        $anniversaryDate = $relationship?->anniversary_date;

        $journeyVideoId = null;
        if ($settings->journey_video_url) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $settings->journey_video_url, $match);
            $journeyVideoId = $match[1] ?? null;
        }

        $journeyVideoId2 = null;
        if ($settings->journey_video_url_2) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $settings->journey_video_url_2, $match);
            $journeyVideoId2 = $match[1] ?? null;
        }
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
    <div class="relative min-h-screen">

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

        {{-- Unified Hero & Carousel Logic --}}
        <div x-data="{ 
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
                        // Advance video after 15 seconds (while looping)
                        this.timer = setTimeout(() => this.next(), 15000);
                    } else {
                        // Regular rotation for images
                        this.timer = setTimeout(() => this.next(), 5000);
                    }
                },
                init() { 
                    this.startRotation();
                }
            }">

            {{-- Hero Section --}}
            <main
                class="relative h-[35vh] md:h-[80vh] w-full flex flex-col items-center justify-center text-center overflow-hidden">
                {{-- Banners Carousel as Background --}}
                <div class="absolute inset-0 z-0 h-full w-full">
                    @forelse($banners as $index => $banner)
                        <div x-show="active === {{ $index }}"
                            x-transition:enter="transition opacity duration-1000 ease-in-out"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition opacity duration-1000 ease-in-out"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute inset-0 w-full h-full">
                            @php
                                $extension = pathinfo($banner, PATHINFO_EXTENSION);
                                $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg', 'mov']);
                            @endphp

                            @if ($isVideo)
                                <video src="{{ Storage::disk('public')->url($banner) }}" class="w-full h-full object-cover"
                                    autoplay loop muted playsinline></video>
                            @else
                                <img src="{{ Storage::disk('public')->url($banner) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                    @empty
                        <div class="absolute inset-0 bg-[#fdfaf6]"></div>
                    @endforelse
                </div>

                {{-- Content Overlay --}}
                <div class="relative z-10 w-full px-3 md:px-12 lg:px-24 pt-24 md:pt-0 max-h-full overflow-hidden"
                    x-data="{ 
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
                            x-transition:leave="transition opacity duration-1000" class="space-y-4">
                            <h1 class="text-sm md:text-3xl font-medium tracking-tight text-white drop-shadow-2xl text-justify"
                                style="text-align-last: justify;">
                                <template x-for="(line, i) in fullText.substring(0, index).split('\n')" :key="i">
                                    <div x-text="line" class="w-full" style="text-align-last: justify;"></div>
                                </template>
                            </h1>
                        </div>
                    @endforeach


                </div>
            </main>

            {{-- Media Carousel Section --}}
            <div class="max-w-7xl mx-auto px-2 md:px-12 mt-12 md:mt-24 mb-12 md:mb-20">
                @if($settings->about_us)
                    <div class="text-center mb-6 md:mb-12">
                        <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">description</h2>
                    </div>
                    <div class="max-w-2xl mx-auto mb-12 md:mb-24 px-4">
                        <div class="text-xs md:text-sm theme-text opacity-60 font-medium tracking-tighter lowercase text-justify" style="text-align-last: justify;">
                            {!! nl2br(e($settings->about_us)) !!}
                        </div>
                    </div>
                @endif

                <div class="text-center mb-8 md:mb-16">
                    <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">our journey</h2>
                </div>
                @if($journeyVideoId || $journeyVideoId2)
                    {{-- YouTube Embed replaces Carousel --}}
                    <div
                        class="grid {{ ($journeyVideoId && $journeyVideoId2) ? 'grid-cols-2' : 'grid-cols-1' }} gap-2 md:gap-8">
                        @if($journeyVideoId)
                            <div
                                class="relative aspect-video rounded-xl md:rounded-2xl overflow-hidden bg-black shadow-2xl border border-white/5">
                                <iframe class="absolute inset-0 w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $journeyVideoId }}?autoplay=1&mute=1&loop=1&playlist={{ $journeyVideoId }}&modestbranding=1&rel=0&iv_load_policy=3&showinfo=0&controls=1"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        @endif
                        @if($journeyVideoId2)
                            <div
                                class="relative aspect-video rounded-xl md:rounded-2xl overflow-hidden bg-black shadow-2xl border border-white/5">
                                <iframe class="absolute inset-0 w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $journeyVideoId2 }}?autoplay=1&mute=1&loop=1&playlist={{ $journeyVideoId2 }}&modestbranding=1&rel=0&iv_load_policy=3&showinfo=0&controls=1"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Original Carousel --}}
                    <div
                        class="relative aspect-[16/9] md:aspect-[21/9] rounded-xl md:rounded-2xl overflow-hidden bg-white/5 shadow-2xl border border-white/5">
                        @foreach($banners as $index => $banner)
                            @php
                                $extension = pathinfo($banner, PATHINFO_EXTENSION);
                                $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg', 'mov']);
                            @endphp

                            @if ($isVideo)
                                <video src="{{ Storage::disk('public')->url($banner) }}" x-show="active === {{ $index }}"
                                    x-transition:enter="transition opacity duration-1000"
                                    x-transition:leave="transition opacity duration-1000"
                                    class="absolute inset-0 w-full h-full object-cover" autoplay loop muted playsinline></video>
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
                @endif

                {{-- Watch Journey Button (Contrast Styled) --}}
                @if($settings->youtube_url)
                    <div class="mt-6 md:mt-10 flex justify-center">
                        <a href="{{ $settings->youtube_url }}" target="_blank"
                            class="px-6 py-2.5 bg-[var(--text-primary)] text-[var(--bg-primary)] shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 text-sm font-semibold rounded-full transition-all duration-500 lowercase flex items-center gap-2">
                            watch all journey
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        @if($anniversaryDate)
        {{-- Times Fly Section --}}
        <section class="relative z-10 max-w-7xl mx-auto px-4 py-8 md:py-16 text-center">
            <div class="mb-6 md:mb-12">
                <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">times fly</h2>
            </div>
            <div x-data="{
                start: new Date('{{ $anniversaryDate }}').getTime(),
                days: 0, hours: 0, minutes: 0, seconds: 0,
                update() {
                    const now = new Date().getTime();
                    const diff = Math.abs(now - this.start);
                    this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                }
            }" x-init="update(); setInterval(() => update(), 1000)" 
            class="flex flex-wrap justify-center gap-x-4 md:gap-x-8 theme-text">
                <span class="text-3xl md:text-7xl font-bold tracking-tighter" x-text="days + 'd'"></span>
                <span class="text-3xl md:text-7xl font-bold tracking-tighter" x-text="hours + 'h'"></span>
                <span class="text-3xl md:text-7xl font-bold tracking-tighter" x-text="minutes + 'm'"></span>
                <span class="text-3xl md:text-7xl font-bold tracking-tighter" x-text="seconds + 's'"></span>
            </div>
            <div class="mt-4 md:mt-8 text-[10px] md:text-sm theme-text opacity-40 lowercase italic tracking-tight">
                and counting every second together...
            </div>
        </section>
        @endif

        {{-- Public Feed --}}
        <section class="relative z-10 max-w-7xl mx-auto px-0 py-8 md:px-4 md:py-24 bg-transparent">
            <div class="text-center mb-6 md:mb-16">
                <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">public stories</h2>
            </div>
            <livewire:publicfeed />
        </section>
        <footer class="py-8 text-center border-t theme-border bg-transparent">
            <p class="text-[11px] opacity-50 tracking-tight">
                All Rights Reserved ©Copyright 2026 {{ $spaceName }}
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