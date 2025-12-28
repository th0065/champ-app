<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

/*
|--------------------------------------------------------------------------
| Routes Protégées (Auth requis)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Tunnel d'achat
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order-success/{id}', [OrderController::class, 'success'])->name('order.success');

    // Paramètres (Settings)
    Route::prefix('settings')->group(function () {
        Route::redirect('/', 'settings/profile');

        Volt::route('profile', 'settings.profile')->name('profile.edit');
        Volt::route('password', 'settings.password')->name('user-password.edit');
        Volt::route('appearance', 'settings.appearance')->name('appearance.edit');

        Volt::route('two-factor', 'settings.two-factor')
            ->middleware(
                Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
                ? ['password.confirm']
                : []
            )
            ->name('two-factor.show');
    });
});


// Ce groupe demande d'être connecté (auth) ET d'être admin (admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Page d'accueil de l'admin (avec la carte et les stats)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Action pour changer le statut d'une commande (ex: Marquer comme livré)
    Route::post('/orders/{id}/status', [AdminController::class, 'updateStatus'])->name('orders.status');
    
});

