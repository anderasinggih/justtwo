<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        \Illuminate\Support\Facades\Log::info('Login attempt from ca69eda', ['email' => $this->form->email]);

        $this->form->authenticate();

        \Illuminate\Support\Facades\Log::info('Login successful');

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard'), navigate: true);
    }
}; ?>

<div class="space-y-6 sm:space-y-8">
    <div class="text-center space-y-1.5">
        <x-ui.heading level="1" size="xl" class="tracking-tighter theme-text lowercase">welcome back</x-ui.heading>
        <p class="text-[11px] theme-text opacity-50 lowercase px-4">we missed you and your beautiful memories.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        @csrf
        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('email')" class="lowercase ml-2 text-xs font-semibold theme-text opacity-50" />
            <x-ui.input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" placeholder="your email address" class="py-2.5 text-xs" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 ml-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between ml-2">
                <x-input-label for="password" :value="__('password')" class="lowercase text-xs font-semibold theme-text opacity-50" />
                @if (Route::has('password.request'))
                    <a class="text-[10px] theme-text opacity-40 hover:opacity-100 transition-colors lowercase" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('forgot password?') }}
                    </a>
                @endif
            </div>

            <x-ui.input wire:model="form.password" id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="your secret password"
                            class="py-2.5 text-xs" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2 ml-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between px-2">
            <label for="remember" class="inline-flex items-center group cursor-pointer">
                <div class="relative flex items-center justify-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="peer h-5 w-5 rounded-full border-gray-100 bg-gray-50 text-brand-500 focus:ring-brand-200 transition-all cursor-pointer" name="remember">
                </div>
                <span class="ms-3 text-sm theme-text opacity-50 group-hover:opacity-100 transition-colors lowercase">{{ __('remember us') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" class="w-full py-3 text-sm rounded-2xl" variant="primary">
                {{ __('log in') }}
            </x-ui.button>
        </div>

        <div class="text-center pt-4">
            <p class="text-sm theme-text opacity-50 lowercase">
                don't have an account? 
                <a href="{{ route('register') }}" class="text-brand-500 font-semibold hover:underline" wire:navigate>
                    join the journey
                </a>
            </p>
        </div>
    </form>
</div>
