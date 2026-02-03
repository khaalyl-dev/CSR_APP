<?php

use App\Http\Controllers\Web\CorporateController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\SiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        $user = Auth::guard('web')->user();
        return redirect()->route(
            $user->role === 'corporate' ? 'corporate.dashboard' : 'site.dashboard'
        );
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/site', [SiteController::class, 'dashboard'])->name('site.dashboard')->middleware('role:plant');
    Route::get('/corporate', [CorporateController::class, 'dashboard'])->name('corporate.dashboard')->middleware('role:corporate');
});

Route::get('/test-auth', function () {
    return view('test-auth');
});
