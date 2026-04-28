<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard'), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="space-y-8">
    <div class="text-center space-y-2">
        <x-ui.heading level="1" size="3xl" class="tracking-tight text-romantic-slate">verify email</x-ui.heading>
        <p class="text-gray-400 lowercase">one small step to start your journey.</p>
    </div>

    <div class="bg-romantic-rose/50 p-4 rounded-2xl text-xs text-brand-600 leading-relaxed lowercase">
        {{ __('thanks for signing up! before getting started, could you verify your email address by clicking on the link we just emailed to you? if you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="font-medium text-xs text-green-600 lowercase bg-green-50 p-3 rounded-xl border border-green-100">
            {{ __('a new verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="flex flex-col space-y-4 pt-2">
        <x-ui.button wire:click="sendVerification" class="w-full py-4 text-base" variant="primary">
            {{ __('resend verification email') }}
        </x-ui.button>

        <x-ui.button wire:click="logout" variant="ghost" class="w-full py-3 text-sm lowercase text-gray-400 hover:text-gray-600">
            {{ __('log out') }}
        </x-ui.button>
    </div>
</div>

