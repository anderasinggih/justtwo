<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $registration_token = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'registration_token' => ['required', 'string', 'in:LVNPC2026'],
        ], [
            'registration_token.in' => 'The registration token is invalid. Please contact support.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div class="space-y-6 sm:space-y-8">
    <div class="text-center space-y-1.5">
        <x-ui.heading level="1" size="xl" class="tracking-tighter theme-text lowercase">join the journey</x-ui.heading>
        <p class="text-[11px] theme-text opacity-50 lowercase px-4">start sharing your beautiful moments together.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Registration Token -->
        <div class="space-y-2">
            <x-input-label for="registration_token" :value="__('registration token')" class="lowercase ml-2 text-xs font-semibold theme-text opacity-50" />
            <x-ui.input wire:model.live="registration_token" id="registration_token"
                            type="text"
                            name="registration_token" required autofocus
                            placeholder="enter invitation token"
                            class="py-2.5 text-xs" />
            <x-input-error :messages="$errors->get('registration_token')" class="mt-2 ml-2" />
        </div>

        @if($registration_token === 'LVNPC2026')
            <!-- Name -->
            <div class="space-y-2" x-transition>
                <x-input-label for="name" :value="__('name')" class="lowercase ml-2 text-xs font-semibold theme-text opacity-50" />
                <x-ui.input wire:model="name" id="name" type="text" name="name" required autocomplete="name" placeholder="your name" class="py-2.5 text-xs" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 ml-2" />
            </div>

            <!-- Email Address -->
            <div class="space-y-2" x-transition>
                <x-input-label for="email" :value="__('email address')" class="lowercase ml-2 text-xs font-semibold theme-text opacity-50" />
                <x-ui.input wire:model="email" id="email" type="email" name="email" required autocomplete="username" placeholder="your@email.com" class="py-2.5 text-xs" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 ml-2" />
            </div>

            <!-- Password -->
            <div class="space-y-2" x-transition>
                <x-input-label for="password" :value="__('password')" class="lowercase ml-2 text-xs font-semibold theme-text opacity-50" />
                <x-ui.input wire:model="password" id="password"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="choose a strong password"
                                class="py-2.5 text-xs" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 ml-2" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2" x-transition>
                <x-input-label for="password_confirmation" :value="__('confirm password')" class="lowercase ml-2 text-xs font-semibold theme-text opacity-50" />
                <x-ui.input wire:model="password_confirmation" id="password_confirmation"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="repeat your password"
                                class="py-2.5 text-xs" />
            </div>

            <div class="pt-4" x-transition>
                <x-ui.button type="submit" class="w-full py-3 text-sm rounded-2xl" variant="primary">
                    {{ __('create account') }}
                </x-ui.button>
            </div>
        @elseif($registration_token !== '')
            <p class="text-[10px] text-center theme-text opacity-30 lowercase">please enter a valid token to continue</p>
        @endif

        <div class="text-center pt-2">
            <p class="text-sm theme-text opacity-50 lowercase">
                already part of the journey? 
                <a href="{{ route('login') }}" class="text-brand-500 font-semibold hover:underline" wire:navigate>
                    log in here
                </a>
            </p>
        </div>
    </form>
</div>
