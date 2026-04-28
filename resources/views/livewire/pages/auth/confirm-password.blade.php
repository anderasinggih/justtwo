<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard'), navigate: true);
    }
}; ?>

<div class="space-y-8">
    <div class="text-center space-y-2">
        <x-ui.heading level="1" size="3xl" class="tracking-tight text-romantic-slate">secure area</x-ui.heading>
        <p class="text-gray-400 lowercase">please confirm your password to continue.</p>
    </div>

    <div class="bg-romantic-rose/50 p-4 rounded-2xl text-xs text-brand-600 leading-relaxed lowercase text-center">
        {{ __('this is a secure area of the application. please confirm your password before continuing.') }}
    </div>

    <form wire:submit="confirmPassword" class="space-y-6">
        <!-- Password -->
        <div class="space-y-2">
            <x-input-label for="password" :value="__('password')" class="lowercase ml-2 text-xs font-semibold text-gray-500" />
            <x-ui.input wire:model="password" id="password" type="password" name="password" required autocomplete="current-password" placeholder="your secret password" autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-2 ml-2" />
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" class="w-full py-4 text-lg" variant="primary">
                {{ __('confirm') }}
            </x-ui.button>
        </div>
    </form>
</div>
