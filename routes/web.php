<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('dashboard');

Route::get('timeline', \App\Livewire\Timeline::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('timeline');

Route::get('journey', \App\Livewire\Journey::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('journey');

Route::get('memories/create', \App\Livewire\CreatePost::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('memories.create');

Route::get('journal', \App\Livewire\Journal::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('journal');

Route::get('milestones', \App\Livewire\Milestones::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('milestones');

Route::get('calendar', \App\Livewire\Calendar::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('calendar');

Route::get('stats', \App\Livewire\Stats::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('stats');
Route::get('profile', \App\Livewire\Profile::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('profile');

Route::get('settings', \App\Livewire\Settings::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('settings');

Route::get('public-settings', \App\Livewire\PublicSettings::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('public-settings');

Route::get('app-settings', \App\Livewire\AppSettings::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('app-settings');

Route::view('onboarding', 'onboarding')
    ->middleware(['auth'])
    ->name('onboarding');

Route::get('invite/{code}', \App\Livewire\InvitePartner::class)
    ->name('invite');


require __DIR__.'/auth.php';
