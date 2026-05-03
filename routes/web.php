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

Route::get('gallery', \App\Livewire\Gallery::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('gallery');

Route::get('gallery/preview/{media}', \App\Livewire\InternalGalleryPreview::class)
    ->middleware(['auth', 'verified'])
    ->name('gallery.preview');

Route::get('memory/new', \App\Livewire\CreatePost::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('memories.create');

Route::get('memories/{post}/edit', \App\Livewire\CreatePost::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('memories.edit');

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

Route::get('settings/space', \App\Livewire\SpaceSettings::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('settings.space');

Route::get('settings/security', \App\Livewire\SecuritySettings::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('settings.security');

Route::get('settings/privacy', \App\Livewire\PrivacySettings::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('settings.privacy');

Route::get('archived', \App\Livewire\ArchivedPosts::class)
    ->middleware(['auth', 'verified', 'relationship'])
    ->name('archived');

Route::view('onboarding', 'onboarding')
    ->middleware(['auth'])
    ->name('onboarding');

Route::get('invite/{code}', \App\Livewire\InvitePartner::class)
    ->name('invite');


Route::get('posts/{post}/preview', \App\Livewire\PublicPostPreview::class)->name('posts.preview');
Route::get('stories/{year}/{month}', \App\Livewire\PublicAlbumDetail::class)->name('public.album');
Route::get('our-journey', \App\Livewire\PublicJourney::class)->name('public.journey');

require __DIR__.'/auth.php';
