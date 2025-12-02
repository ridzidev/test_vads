<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceDeskController;

// Public Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// CAPTCHA Routes (Public)
Route::post('/refresh-captcha', [AuthController::class, 'refreshCaptcha'])->name('refresh-captcha');
Route::get('/refresh-captcha', [AuthController::class, 'refreshCaptcha'])->name('refresh-captcha.get');

// Protected Routes - Customer
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Chat Routes
    Route::get('/chat-room', function () {
        return view('chat-room');
    })->name('chat-room');
});

// Protected Routes - Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $totalUsers = \App\Models\User::where('role', 'customer')->where('is_active', true)->count();
        $recentUsers = \App\Models\User::where('role', 'customer')->latest()->take(5)->get();
        return view('admin-dashboard', compact('totalUsers', 'recentUsers'));
    })->name('dashboard');

    Route::get('/service-desk-dashboard', function () {
        return view('service-desk-dashboard');
    })->name('service-desk-dashboard');

    Route::get('/master-customer', function () {
        return view('master-customer');
    })->name('master-customer');
});

// Alternative routes for easier access
Route::get('/admin-dashboard', function () {
    $totalUsers = \App\Models\User::where('role', 'customer')->where('is_active', true)->count();
    $recentUsers = \App\Models\User::where('role', 'customer')->latest()->take(5)->get();
    return view('admin-dashboard', compact('totalUsers', 'recentUsers'));
})->middleware('auth')->name('admin-dashboard');

Route::get('/service-desk-dashboard', [ServiceDeskController::class, 'dashboard'])->middleware('auth')->name('service-desk-dashboard');

Route::get('/master-customer', function () {
    return view('master-customer');
})->middleware('auth')->name('master-customer');

Route::get('/sd-dashboard', function () {
    return redirect('/service-desk-dashboard');
})->middleware('auth')->name('sd-dashboard');

// API Routes
Route::get('/master-customer-api', [CustomerController::class, 'index']);
