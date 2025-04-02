<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', static function () {
    return view('welcome');
})->name('home');

Route::get('/about', static function () {
    return view('front-end.about');
})->name('about');

Route::get('/history', static function () {
    return view('front-end.history');
})->name('history');

Route::get('/memorial', static function () {
    return view('front-end.memorial');
})->name('memorial');

Route::get('/chapel', static function () {
    return view('front-end.chapel');
})->name('chapel');

Route::get('/museum', static function () {
    return view('front-end.museum');
})->name('museum');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('contact', 'auth.contact')->name('contact');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    /**
     * Routes to volt blade files on the back end of the app
     */
    Volt::route('mail', 'mail.index.page')->name('mail.index')->middleware('can:mail-index');
    Volt::route('users', 'user.index.page')->name('user.index')->middleware('can:user-index');
    Volt::route('registrants', 'registrant.index.page')->name('registrant.index')->middleware('can:registrant-index');
    Volt::route('articles', 'article.index.page')->name('article.index')->middleware('can:article-index');
    Volt::route('stories', 'story.index.page')->name('story.index')->middleware('can:story-index');
    Volt::route('posts', 'post.index.page')->name('post.index')->middleware('can:post-index');
    Volt::route('events', 'event.index.page')->name('event.index')->middleware('can:event-index');
    Volt::route('media', 'media.index.page')->name('media.index')->middleware('can:media-index');
});

require __DIR__.'/auth.php';
