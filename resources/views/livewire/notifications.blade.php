<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-full transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-4 h-4 bg-brand-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="absolute right-0 mt-2 w-80 bg-white border border-gray-100 rounded-3xl shadow-xl z-50 overflow-hidden">
        
        <div class="p-4 border-b border-gray-50 flex items-center justify-between">
            <x-ui.heading level="3" size="sm">Notifications</x-ui.heading>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-brand-500 hover:underline lowercase">mark all read</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div @click="$wire.markAsRead('{{ $notification->id }}')" 
                     class="p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors cursor-pointer {{ $notification->unread() ? 'bg-brand-50/30' : '' }}">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-romantic-rose flex items-center justify-center text-brand-500 shrink-0">
                            {{-- Icon based on type --}}
                            @if(($notification->data['type'] ?? '') === 'reaction')
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs text-gray-800 lowercase">
                                <span class="font-semibold">{{ $notification->data['user_name'] ?? 'someone' }}</span> 
                                {{ $notification->data['message'] ?? 'did something' }}
                            </p>
                            <p class="text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <p class="text-gray-300 text-sm lowercase italic">no notifications</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
