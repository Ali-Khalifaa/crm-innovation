<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\LandingContactController;
use App\Http\Controllers\LandingNewsletterController;
use App\Http\Middleware\SetLandingLocale;
use Illuminate\Support\Facades\Route;

// Language switch
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// =====================================================
// Landing Page Routes
// =====================================================
Route::middleware(SetLandingLocale::class)->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');
    Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
    Route::get('/register', [LandingController::class, 'register'])->name('register');
    Route::get('/login', [LandingController::class, 'login'])->name('login');
    Route::get('/privacy', [LandingController::class, 'privacy'])->name('landing.privacy');
    Route::get('/terms', [LandingController::class, 'terms'])->name('landing.terms');
    Route::post('/contact', [LandingContactController::class, 'send'])->name('contact.send');
    Route::post('/newsletter/subscribe', [LandingNewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
});

// =====================================================
// CRM SPA (Vue 3) — catch-all under /crm/*
// =====================================================
Route::get('/crm/{any?}', function () {
    return view('crm');
})->where('any', '.*')->name('crm');

// =====================================================
// Super Admin SPA — catch-all under /admin/*
// =====================================================
Route::get('/admin/{any?}', function () {
    return view('welcome');
})->where('any', '.*')->name('admin');
