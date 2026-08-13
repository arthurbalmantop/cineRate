<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');

Route::livewire('/register', 'auth.register')
    ->middleware('guest')
    ->name('register');

Route::livewire('/feed', 'feed')
    ->middleware('auth')
    ->name('feed');

Route::livewire('/posts/create', 'posts.create')
    ->middleware('auth')
    ->name('posts.create');

Route::livewire('/profile', 'profile.edit')
    ->middleware('auth')
    ->name('profile.edit');

Route::livewire('/followinf', 'following.index')
    ->middleware('auth')
    ->name('following.index');

Route::livewire('/my-posts', 'posts.mine')
    ->middleware('auth')
    ->name('posts.mine');

Route::post('/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');

})
    ->middleware('auth')
    ->name('logout');

Route::livewire('/forgot-password', 'auth.forgot-password')
    ->middleware('guest')
    ->name('password.request');

Route::livewire('/reset-password/{token}', 'auth.reset-password')
    ->middleware('guest')
    ->name('password.reset');