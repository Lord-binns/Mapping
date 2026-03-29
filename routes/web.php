<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PinController as AdminPinController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::get('/reports', [\App\Http\Controllers\Admin\PinController::class, 'reports'])->name('reports');
    Route::get('/heatmap', [\App\Http\Controllers\Admin\PinController::class, 'heatmap'])->name('heatmap');
    Route::view('/hotspots', 'admin.hotspots')->name('hotspots');
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/settings', 'admin.settings')->name('settings');
    Route::resource('pins', AdminPinController::class);
    Route::patch('/pins/{pin}/verify', [AdminPinController::class, 'verify'])->name('pins.verify');
    Route::patch('/pins/{pin}/reject', [AdminPinController::class, 'reject'])->name('pins.reject');
});
Route::get('/api/pins', function () {
    return response()->json(\App\Models\Pin::where('status', 'verified')->with('user:id,name')->get());
});

Route::get('/api/pins/pending', function () {
    return response()->json(\App\Models\Pin::where('status', 'pending')->with('user:id,name')->latest()->get());
});

Route::post('/pins', [\App\Http\Controllers\PinController::class, 'store'])->name('pins.store');
