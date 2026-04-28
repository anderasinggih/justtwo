<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="contents">
    {{-- Bottom Navigation Bar --}}
    <div class="fixed bottom-1 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md" wire:persist="main-nav">
        <nav x-data="{ 
                active: @js(
                    request()->routeIs('dashboard') ? 0 : 
                    (request()->routeIs('timeline') ? 1 : 
                    (request()->routeIs('journey') ? 3 : 
                    (request()->routeIs('profile') || request()->routeIs('settings') || request()->routeIs('stats') ? 4 : 2)))
                ),
                ready: false,
                init() {
                    this.ready = true;
                    sessionStorage.setItem('nav-active', this.active);
                },
                setPage(index) {
                    this.active = index;
                    sessionStorage.setItem('nav-active', index);
                }
            }" 
            class="relative bg-black/40 backdrop-blur-xl border border-white/10 rounded-full p-1 flex items-center ring-1 ring-white/10 text-white overflow-hidden">
            
            {{-- Sliding Bubble Indicator --}}
            <div class="absolute inset-y-1.5 w-[calc(20%-1.6px)] pointer-events-none flex justify-center"
                 :class="ready ? 'opacity-100 transition-all duration-700 ease-[cubic-bezier(0.19,1,0.22,1)]' : 'opacity-0'"
                 :style="`transform: translateX(${active * 100}%)`"
                 style="transform: translateX({{ 
                    request()->routeIs('dashboard') ? '0' : 
                    (request()->routeIs('timeline') ? '100%' : 
                    (request()->routeIs('journey') ? '300%' : 
                    (request()->routeIs('profile') || request()->routeIs('settings') || request()->routeIs('stats') ? '400%' : '200%')))
                 }})">
                <div class="aspect-square h-full bg-white/20 rounded-full shadow-[inset_0_1px_2px_rgba(255,255,255,0.2)]"></div>
            </div>

            {{-- Home --}}
            <a href="{{ route('dashboard') }}" wire:navigate @click="setPage(0)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-opacity duration-500 {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </a>

            {{-- Timeline --}}
            <a href="{{ route('timeline') }}" wire:navigate @click="setPage(1)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-opacity duration-500 {{ request()->routeIs('timeline') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </a>

            <div class="relative z-10 flex-1 flex justify-center">
                <a href="{{ route('memories.create') }}" wire:navigate
                    class="bg-brand-500 text-white p-2.5 rounded-full hover:scale-110 active:scale-95 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </a>
            </div>

            {{-- Journey (Icon: Clock/Time) --}}
            <a href="{{ route('journey') }}" wire:navigate @click="setPage(3)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-opacity duration-500 {{ request()->routeIs('journey') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </a>

            {{-- Profile --}}
            <a href="{{ route('profile') }}" wire:navigate @click="setPage(4)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-all duration-300">
                <div class="p-0.5 rounded-full border-2 {{ request()->routeIs('profile') || request()->routeIs('settings') || request()->routeIs('stats') ? 'border-white' : 'border-transparent opacity-40 hover:opacity-100' }}">
                    <img src="{{ Auth::user()->profile_photo_url }}"
                        class="w-5 h-5 rounded-full object-cover">
                </div>
            </a>
        </nav>
    </div>
</div>