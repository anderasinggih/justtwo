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
    <div class="fixed bottom-3 sm:bottom-8 left-1/2 -translate-x-1/2 z-50 w-[92%] sm:w-auto sm:min-w-[400px] max-w-md" wire:persist="main-nav">
        <nav x-data="{ 
                active: @js(
                    request()->routeIs('dashboard') ? 0 : 
                    (request()->routeIs('planner*') ? 1 : 
                    (request()->routeIs('gallery') ? 3 : 
                    (request()->routeIs('profile*') || request()->routeIs('settings') || request()->routeIs('stats') ? 4 : 2)))
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
            <div class="absolute inset-y-1.5 w-[calc(20%-1.6px)] pointer-events-none flex justify-center px-1"
                 :class="ready ? 'opacity-100 transition-all duration-700 ease-[cubic-bezier(0.19,1,0.22,1)]' : 'opacity-0'"
                 :style="`transform: translateX(${active * 100}%)`"
                 style="transform: translateX({{ 
                    request()->routeIs('dashboard') ? '0' : 
                    (request()->routeIs('gallery') ? '300%' : 
                    (request()->routeIs('profile') || request()->routeIs('settings') || request()->routeIs('stats') ? '400%' : '200%'))
                 }})">
                <div class="w-full h-full bg-white/20 rounded-full shadow-[inset_0_1px_2px_rgba(255,255,255,0.2)]"></div>
            </div>

            {{-- Home --}}
            <a href="/dashboard" wire:navigate.hover @click="setPage(0)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-opacity duration-500 {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </a>

            {{-- Planner --}}
            <a href="/planner" wire:navigate.hover @click="setPage(1)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-opacity duration-500 {{ request()->routeIs('planner*') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </a>

            <div class="relative z-10 flex-1 flex justify-center">
                <a href="/memory/new" wire:navigate.hover @click="setPage(2)"
                    class="py-2.5 transition-opacity duration-500 {{ request()->routeIs('memory.new') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </a>
            </div>

            {{-- Gallery --}}
            <a href="/gallery" wire:navigate.hover @click="setPage(3)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-opacity duration-500 {{ request()->routeIs('gallery') ? 'opacity-100' : 'opacity-40 hover:opacity-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </a>

            {{-- Profile --}}
            <a href="/profile" wire:navigate.hover @click="setPage(4)"
                class="relative z-10 flex-1 flex justify-center py-2.5 transition-all duration-300">
                <div class="p-0.5 rounded-full border-2 {{ request()->routeIs('profile') || request()->routeIs('settings') || request()->routeIs('stats') ? 'border-white' : 'border-transparent opacity-40 hover:opacity-100' }}">
                    <img src="{{ Auth::user()->profile_photo_url }}"
                        class="w-5 h-5 rounded-full object-cover">
                </div>
            </a>
        </nav>
    </div>
</div>