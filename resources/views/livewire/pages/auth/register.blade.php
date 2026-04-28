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

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div class="space-y-8">
    <div class="text-center space-y-2">
        <x-ui.heading level="1" size="3xl" class="tracking-tight text-romantic-slate">join the journey</x-ui.heading>
        <p class="text-gray-400 lowercase">start sharing your beautiful moments together.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div class="space-y-2">
            <x-input-label for="name" :value="__('name')" class="lowercase ml-2 text-xs font-semibold text-gray-500" />
            <x-ui.input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" placeholder="your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 ml-2" />
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('email address')" class="lowercase ml-2 text-xs font-semibold text-gray-500" />
            <x-ui.input wire:model="email" id="email" type="email" name="email" required autocomplete="username" placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 ml-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <x-input-label for="password" :value="__('password')" class="lowercase ml-2 text-xs font-semibold text-gray-500" />
            <x-ui.input wire:model="password" id="password"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="choose a strong password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 ml-2" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <x-input-label for="password_confirmation" :value="__('confirm password')" class="lowercase ml-2 text-xs font-semibold text-gray-500" />
            <x-ui.input wire:model="password_confirmation" id="password_confirmation"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="repeat your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 ml-2" />
        </div>

        <div class="pt-4">
            <x-ui.button type="submit" class="w-full py-4 text-lg" variant="primary">
                {{ __('create account') }}
            </x-ui.button>
        </div>

        <div class="text-center pt-2">
            <p class="text-sm text-gray-400 lowercase">
                already part of the journey? 
                <a href="{{ route('login') }}" class="text-brand-500 font-semibold hover:underline" wire:navigate>
                    log in here
                </a>
            </p>
        </div>
    </form>
</div>
