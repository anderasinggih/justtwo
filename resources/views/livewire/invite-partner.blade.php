<div class="space-y-8">
    <div class="text-center space-y-2">
        <x-ui.heading level="1" size="3xl" class="tracking-tight text-romantic-slate">join the shared space</x-ui.heading>
        @if($relationship)
            <p class="text-gray-400 lowercase">you've been invited by <span class="font-semibold text-brand-500">{{ $relationship->creator->name }}</span> to preserve memories together.</p>
        @else
            <p class="text-gray-400 lowercase">this invite link seems to be invalid or expired.</p>
        @endif
    </div>

    @if($relationship)
        <div class="bg-romantic-rose/50 p-6 rounded-[2rem] text-center space-y-4">
            <div class="w-20 h-20 bg-white rounded-full mx-auto flex items-center justify-center shadow-sm">
                <x-application-logo class="w-12 h-12 text-brand-500" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-romantic-slate lowercase">{{ $relationship->name }}</h3>
                <p class="text-xs text-gray-500 lowercase">shared private gallery</p>
            </div>
        </div>

        <div class="pt-4">
            @if(Auth::check())
                <x-ui.button wire:click="acceptInvite" class="w-full py-4 text-lg" variant="primary">
                    {{ __('accept invitation') }}
                </x-ui.button>
            @else
                <div class="space-y-4">
                    <p class="text-sm text-center text-gray-500 lowercase">please log in or register to join.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.button href="{{ route('login', ['redirect' => url()->current()]) }}" class="w-full" variant="outline">
                            {{ __('log in') }}
                        </x-ui.button>
                        <x-ui.button href="{{ route('register', ['redirect' => url()->current()]) }}" class="w-full" variant="primary">
                            {{ __('register') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="pt-4">
            <x-ui.button href="{{ route('dashboard') }}" class="w-full py-4 text-lg" variant="outline">
                {{ __('back home') }}
            </x-ui.button>
        </div>
    @endif

    <x-auth-session-status class="mt-4" :status="session('error')" />
</div>
