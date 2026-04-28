<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="space-y-8">
    <div class="text-center space-y-2">
        <x-ui.heading level="1" size="3xl" class="tracking-tight text-romantic-slate">forgot password?</x-ui.heading>
        <p class="text-gray-400 lowercase">no worries, we'll help you find your way back.</p>
    </div>

    <div class="bg-romantic-rose/50 p-4 rounded-2xl text-xs text-brand-600 leading-relaxed lowercase">
        {{ __('just let us know your email address and we will email you a password reset link.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-6">
        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('email address')" class="lowercase ml-2 text-xs font-semibold text-gray-500" />
            <x-ui.input wire:model="email" id="email" type="email" name="email" required autofocus placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 ml-2" />
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" class="w-full py-4 text-base" variant="primary">
                {{ __('email password reset link') }}
            </x-ui.button>
        </div>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-brand-500 transition-colors lowercase" wire:navigate>
                {{ __('back to login') }}
            </a>
        </div>
    </form>
</div>
