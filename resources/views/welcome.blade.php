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

        $spotifyEmbedUrl = null;
        if ($settings->spotify_url) {
            if (preg_match('/spotify\.com\/(playlist|track|album|artist)\/([a-zA-Z0-9]+)/', $settings->spotify_url, $matches)) {
                $spotifyEmbedUrl = "https://open.spotify.com/embed/{$matches[1]}/{$matches[2]}?utm_source=generator&autoplay=1";
            }
        }
    @endphp

    <title>{{ $spaceName }} — {{ $settings->hero_title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{
        currentTheme: '{{ $settings->theme ?? 'light' }}',
        themes: ['light', 'dark', 'rose', 'midnight'],
        init() {
            if (this.currentTheme === 'mix') {
                this.currentTheme = this.themes[Math.floor(Math.random() * this.themes.length)];
                setInterval(() => {
                    let idx = this.themes.indexOf(this.currentTheme);
                    this.currentTheme = this.themes[(idx + 1) % this.themes.length];
                }, 300000); // 5 minutes
            }
        }
    }" 
    :data-theme="currentTheme"
    :class="currentTheme"
    class="antialiased font-sans theme-text theme-bg transition-colors duration-1000">
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
                class="relative h-[30vh] md:h-[45vh] lg:h-[80vh] w-full flex flex-col items-center justify-center text-center overflow-hidden">
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
                            <h1 class="text-sm md:text-4xl lg:text-6xl font-medium tracking-tight text-white drop-shadow-2xl text-justify"
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
                    <div class="text-center mb-8 md:mb-12 page-reveal reveal-delay-1 reveal">
                        <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">description</h2>
                    </div>
                    <div class="max-w-5xl mx-auto mb-12 md:mb-24 px-4 md:px-30 lg:px-50 page-reveal reveal-delay-2 reveal">
                        <div class="text-xs md:text-lg theme-text opacity-60 font-medium tracking-tighter lowercase text-justify leading-relaxed"
                            style="text-align-last: justify;">
                            {!! nl2br(e($settings->about_us)) !!}
                        </div>
                    </div>
                @endif

                <div class="text-center mb-8 md:mb-12 page-reveal reveal-delay-3 reveal">
                    <h2 class="text-xl md:text-4xl font-bold tracking-tighter lowercase">our journey</h2>
                </div>
                @if($journeyVideoId || $journeyVideoId2)
                    {{-- YouTube Embed replaces Carousel --}}
                    <div class="max-w-6xl mx-auto px-2">
                        @php
                            $embedBg = match ($settings->theme ?? 'light') {
                                'dark', 'midnight' => 'bg-black',
                                'rose' => 'bg-rose-200/50',
                                default => 'bg-white',
                            };
                        @endphp
                        <div
                            class="grid {{ ($journeyVideoId && $journeyVideoId2) ? 'grid-cols-2' : 'grid-cols-1' }} gap-2 md:gap-8">
                            @if($journeyVideoId)
                                <div
                                    class="relative aspect-video rounded-xl md:rounded-2xl overflow-hidden {{ $embedBg }} shadow-2xl border theme-border">
                                    <iframe id="yt-player-1" class="absolute inset-0 w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $journeyVideoId }}?autoplay=1&mute=0&loop=1&playlist={{ $journeyVideoId }}&enablejsapi=1&modestbranding=1&rel=0&iv_load_policy=3&showinfo=0&controls=1&vq=hd1080"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                </div>
                            @endif
                            @if($journeyVideoId2)
                                <div
                                    class="relative aspect-video rounded-xl md:rounded-2xl overflow-hidden {{ $embedBg }} shadow-2xl border theme-border">
                                    <iframe id="yt-player-2" class="absolute inset-0 w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $journeyVideoId2 }}?autoplay=1&mute=0&loop=1&playlist={{ $journeyVideoId2 }}&enablejsapi=1&modestbranding=1&rel=0&iv_load_policy=3&showinfo=0&controls=1&vq=hd1080"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                </div>
                            @endif
                        </div>

                        <script src="https://www.youtube.com/iframe_api"></script>
                        <script>
                            function onYouTubeIframeAPIReady() {
                                [1, 2].forEach(id => {
                                    const el = document.getElementById('yt-player-' + id);
                                    if (el) {
                                        new YT.Player('yt-player-' + id, {
                                            events: {
                                                'onReady': function(event) {
                                                    event.target.setVolume(70);
                                                }
                                            }
                                        });
                                    }
                                });
                            }
                        </script>
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
            <section class="relative z-10 max-w-7xl mx-auto px-4 py-12 md:py-20 text-center page-reveal reveal-delay-4 reveal">
                <div class="mb-8 md:mb-12">
                    <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">times fly</h2>
                </div>
                <div x-data="{
                                start: new Date('{{ $anniversaryDate }}').getTime(),
                                days: 0, hours: 0, minutes: 0, seconds: 0,
                                actualTotalSeconds: 0,
                                animated: false,
                                updateActual() {
                                    const now = new Date().getTime();
                                    const diff = Math.abs(now - this.start);
                                    this.actualTotalSeconds = Math.floor(diff / 1000);
                                    
                                    // Current values (for real-time ticking)
                                    this.days = Math.floor(this.actualTotalSeconds / (60 * 60 * 24));
                                    this.hours = Math.floor((this.actualTotalSeconds % (60 * 60 * 24)) / (60 * 60));
                                    this.minutes = Math.floor((this.actualTotalSeconds % (60 * 60)) / 60);
                                    this.seconds = this.actualTotalSeconds % 60;
                                },
                                animate() {
                                    if (this.animated) return;
                                    this.animated = true;
                                    
                                    const now = new Date().getTime();
                                    const diff = Math.abs(now - this.start);
                                    const targetTotal = Math.floor(diff / 1000);
                                    
                                    const duration = 2500; // 2.5 seconds
                                    const startTime = performance.now();
                                    
                                    const step = (timestamp) => {
                                        const progress = Math.min((timestamp - startTime) / duration, 1);
                                        const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                                        
                                        const currentTotal = Math.floor(ease * targetTotal);
                                        
                                        this.days = Math.floor(currentTotal / (60 * 60 * 24));
                                        this.hours = Math.floor((currentTotal % (60 * 60 * 24)) / (60 * 60));
                                        this.minutes = Math.floor((currentTotal % (60 * 60)) / 60);
                                        this.seconds = currentTotal % 60;
                                        
                                        if (progress < 1) {
                                            requestAnimationFrame(step);
                                        } else {
                                            setInterval(() => this.updateActual(), 1000);
                                        }
                                    };
                                    requestAnimationFrame(step);
                                }
                            }" 
                            x-init="
                                const observer = new IntersectionObserver((entries) => {
                                    if (entries[0].isIntersecting) animate();
                                }, { threshold: 0.5 });
                                observer.observe($el);
                            "
                    class="flex flex-wrap justify-center gap-x-4 md:gap-x-10 theme-text">
                    <div class="flex items-baseline gap-0.5">
                        <span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums inline-block min-w-[1.2ch] md:min-w-[1.5ch] text-right" x-text="days"></span>
                        <span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">d</span>
                    </div>
                    <div class="flex items-baseline gap-0.5">
                        <span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums inline-block min-w-[1.2ch] md:min-w-[1.5ch] text-right" x-text="hours"></span>
                        <span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">h</span>
                    </div>
                    <div class="flex items-baseline gap-0.5">
                        <span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums inline-block min-w-[1.2ch] md:min-w-[1.5ch] text-right" x-text="minutes"></span>
                        <span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">m</span>
                    </div>
                    <div class="flex items-baseline gap-0.5">
                        <span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums inline-block min-w-[1.2ch] md:min-w-[1.5ch] text-right" x-text="seconds"></span>
                        <span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">s</span>
                    </div>
                </div>
                <div class="mt-4 md:mt-8 text-[10px] md:text-sm theme-text opacity-40 lowercase italic tracking-tight">
                    and counting every second together...
                </div>
            </section>
        @endif

        @if($spotifyEmbedUrl)
            {{-- Spotify Soundtrack Section --}}
            <section class="relative z-10 max-w-2xl mx-auto px-4 py-12 md:py-20 page-reveal reveal-delay-5 reveal">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">our soundtrack</h2>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-2xl border theme-border">
                    <iframe style="border-radius:12px" src="{{ $spotifyEmbedUrl }}" width="100%" height="152"
                        frameBorder="0" allowfullscreen=""
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading="lazy"></iframe>
                </div>
            </section>
        @endif

        {{-- Public Feed --}}
        <section class="relative z-10 max-w-7xl mx-auto px-0 py-12 md:px-4 md:py-20 bg-transparent reveal">
            <div class="text-center mb-8 md:mb-12">
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: stop observing once revealed
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => {
                observer.observe(el);
            });
        });

        // For Livewire navigations
        document.addEventListener('livewire:navigated', () => {
            document.querySelectorAll('.reveal').forEach(el => {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('active');
                        }
                    });
                }, { threshold: 0.1 });
                observer.observe(el);
            });
        });
    </script>
    @livewireScripts
</body>

</html>