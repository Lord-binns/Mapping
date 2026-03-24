<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/reports', 'admin.reports')->name('reports');
    Route::view('/hotspots', 'admin.hotspots')->name('hotspots');
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/settings', 'admin.settings')->name('settings');
});
