<div class="relative min-h-screen">
    {{-- Nav --}}
    <nav class="relative z-10 max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-8 flex justify-between items-center mix-blend-difference text-white">
        <div class="text-xl font-bold tracking-tighter lowercase">{{ $spaceName }}</div>
        <div class="space-x-4">
            @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium hover:text-brand-200 transition-colors lowercase">dashboard</a>
            @else
                <a href="/login" wire:navigate class="text-sm font-medium hover:text-brand-200 transition-colors lowercase">login</a>
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
                    this.titleTimer = setTimeout(() => { this.showTitle = false; }, 4000);
                    this.timer = setTimeout(() => this.next(), 15000);
                } else {
                    this.timer = setTimeout(() => this.next(), 5000);
                }
            },
            init() { 
                this.startRotation();
                this.$watch('active', () => {
                    this.$nextTick(() => {
                        const videos = this.$el.querySelectorAll('video');
                        videos.forEach((v) => {
                            if (v.offsetParent !== null) {
                                v.currentTime = 0;
                                v.play().catch(() => {});
                            } else {
                                v.pause();
                            }
                        });
                    });
                });
            }
        }">

        {{-- Hero Section --}}
        <main class="relative h-[30vh] md:h-[45vh] lg:h-[80vh] w-full flex flex-col items-center justify-center text-center overflow-hidden">
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
                            <video src="{{ Storage::disk('public')->url($banner) }}" class="w-full h-full object-cover" autoplay loop muted playsinline></video>
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
                        <h1 class="text-sm md:text-2xl lg:text-4xl font-medium tracking-tight text-white drop-shadow-2xl text-justify"
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
                <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">our journey</h2>
            </div>
            @if($journeyVideoId || $journeyVideoId2)
                <div class="max-w-6xl mx-auto px-2">
                    @php
                        $embedBg = match ($settings->theme ?? 'light') {
                            'dark', 'midnight' => 'bg-black',
                            'rose' => 'bg-rose-200/50',
                            default => 'bg-white',
                        };
                    @endphp
                    <div class="grid {{ ($journeyVideoId && $journeyVideoId2) ? 'grid-cols-2' : 'grid-cols-1' }} gap-2 md:gap-8">
                        @if($journeyVideoId)
                            <div class="relative aspect-video rounded-xl md:rounded-2xl overflow-hidden {{ $embedBg }} shadow-2xl group">
                                <iframe id="yt-player-1" class="absolute inset-0 w-full h-full md:w-full md:h-full md:scale-100 mobile-yt-scale"
                                    src="https://www.youtube.com/embed/{{ $journeyVideoId }}?autoplay=1&mute=1&loop=1&playlist={{ $journeyVideoId }}&enablejsapi=1&modestbranding=1&rel=0&iv_load_policy=3&showinfo=0&controls=1&vq=hd1080"
                                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @endif
                        @if($journeyVideoId2)
                            <div class="relative aspect-video rounded-xl md:rounded-2xl overflow-hidden {{ $embedBg }} shadow-2xl group">
                                <iframe id="yt-player-2" class="absolute inset-0 w-full h-full md:w-full md:h-full md:scale-100 mobile-yt-scale"
                                    src="https://www.youtube.com/embed/{{ $journeyVideoId2 }}?autoplay=1&mute=1&loop=1&playlist={{ $journeyVideoId2 }}&enablejsapi=1&modestbranding=1&rel=0&iv_load_policy=3&showinfo=0&controls=1&vq=hd1080"
                                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        @endif
                    </div>

                    <script src="https://www.youtube.com/iframe_api"></script>
                    <script>
                        var ytPlayers = [];
                        function onYouTubeIframeAPIReady() {
                            [1, 2].forEach(id => {
                                const el = document.getElementById('yt-player-' + id);
                                if (el) {
                                    var player = new YT.Player('yt-player-' + id, {
                                        events: {
                                            'onReady': function (event) {
                                                event.target.mute();
                                                event.target.playVideo();
                                            }
                                        }
                                    });
                                    ytPlayers.push(player);
                                }
                            });
                        }
                        document.addEventListener('click', function () {
                            ytPlayers.forEach(p => {
                                if (p && typeof p.playVideo === 'function') { p.playVideo(); }
                            });
                        }, { once: true });
                    </script>
                </div>
            @endif

            @if($settings->youtube_url)
                <div class="mt-6 md:mt-10 flex justify-center">
                    <a href="{{ route('public.journey') }}" wire:navigate
                        class="px-6 py-2.5 bg-[var(--text-primary)] text-[var(--bg-primary)] shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 text-sm font-semibold rounded-full transition-all duration-500 lowercase flex items-center gap-2">
                        watch all journey
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($anniversaryDate)
        <section class="relative z-10 max-w-7xl mx-auto px-4 py-12 md:py-20 text-center page-reveal reveal-delay-4 reveal">
            <div class="mb-8 md:mb-12">
                <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">times fly</h2>
            </div>
            <div x-data="{
                                start: new Date('{{ $anniversaryDate }}').getTime(),
                                days: 0, hours: 0, minutes: 0, seconds: 0,
                                updateActual() {
                                    const now = new Date().getTime();
                                    const diff = Math.abs(now - this.start);
                                    const totalSeconds = Math.floor(diff / 1000);
                                    this.days = Math.floor(totalSeconds / (60 * 60 * 24));
                                    this.hours = Math.floor((totalSeconds % (60 * 60 * 24)) / (60 * 60));
                                    this.minutes = Math.floor((totalSeconds % (60 * 60)) / 60);
                                    this.seconds = totalSeconds % 60;
                                },
                                animate() {
                                    setInterval(() => this.updateActual(), 1000);
                                }
                            }" x-init="animate()" class="flex flex-wrap justify-center gap-x-4 md:gap-x-10 theme-text">
                <div class="flex items-baseline gap-0.5"><span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums" x-text="days"></span><span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">d</span></div>
                <div class="flex items-baseline gap-0.5"><span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums" x-text="hours"></span><span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">h</span></div>
                <div class="flex items-baseline gap-0.5"><span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums" x-text="minutes"></span><span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">m</span></div>
                <div class="flex items-baseline gap-0.5"><span class="text-3xl md:text-7xl font-bold tracking-[-0.07em] tabular-nums" x-text="seconds"></span><span class="text-2xl md:text-5xl font-bold tracking-[-0.07em]">s</span></div>
            </div>
            <div class="mt-4 md:mt-8 text-[10px] md:text-sm theme-text opacity-40 lowercase italic tracking-tight">
                and counting every second together...
            </div>
        </section>
    @endif

    @if($spotifyEmbedUrl)
        <section class="relative z-10 max-w-2xl mx-auto px-4 py-12 md:py-20 page-reveal reveal-delay-5 reveal">
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">our soundtrack</h2>
            </div>
            <div class="rounded-3xl overflow-hidden shadow-2xl border theme-border">
                <iframe style="border-radius:12px" src="{{ $spotifyEmbedUrl }}" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
            </div>
        </section>
    @endif

    <section class="relative z-10 max-w-7xl mx-auto px-0 py-12 md:px-4 md:py-20 bg-transparent reveal">
        <div class="text-center mb-8 md:mb-12">
            <h2 class="text-xl md:text-3xl font-bold tracking-tighter lowercase">public stories</h2>
        </div>
        <livewire:publicfeed />
    </section>

    <footer class="py-8 text-center border-t theme-border bg-transparent">
        <p class="text-[11px] opacity-50 tracking-tight">All Rights Reserved ©Copyright 2026 {{ $spaceName }}</p>
    </footer>

    <style>
        @media (max-width: 768px) {
            .mobile-yt-scale {
                width: 150% !important;
                height: 150% !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) scale(0.6666) !important;
            }
        }
    </style>
</div>
