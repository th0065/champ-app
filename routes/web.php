<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\DriverController;
use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Delivery\Index as DeliveryIndex;
use App\Livewire\Admin\UserManagement;
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
| Routes Protégées (Connexion requise)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
     // Ajoutez cette ligne pour corriger l'erreur :
   Route::get('/admin/users', UserManagement::class)->name('users.index');
    
    // DASHBOARD UNIQUE : Aiguillage automatique selon le rôle
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'admin'  => (new AdminController())->index(),
        'driver' => (new DriverController())->index(),
        'buyer'  => (new BuyerController())->index(),
        default  => abort(403, 'Rôle non reconnu.'),
    };
})->name('dashboard');
    
// routes/web.php


    // Tunnel d'achat
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order-success/{id}', [OrderController::class, 'success'])->name('order.success');

    // Paramètres (Profil, Sécurité, Apparence)
    Route::prefix('settings')->group(function () {
        Route::redirect('/', 'settings/profile');
        Volt::route('profile', 'settings.profile')->name('profile.edit');
        Volt::route('password', 'settings.password')->name('user-password.edit');
        Volt::route('appearance', 'settings.appearance')->name('appearance.edit');
        Volt::route('two-factor', 'settings.two-factor')
            ->middleware(
                Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
                ? ['password.confirm'] : []
            )->name('two-factor.show');
    });

    Route::get('/orders', OrdersIndex::class)->middleware(['auth'])->name('orders.index');

    Route::get('/products', ProductsIndex::class)->middleware(['auth'])->name('products.index');

    Route::get('/delivery',DeliveryIndex::class)->middleware(['auth'])->name('delivery.index');

    /*
    |--------------------------------------------------------------------------
    | Routes d'Administration (Middleware Admin requis)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // Route pour changer le statut d'une commande (ex: Marquer comme livré)
        Route::post('/orders/{id}/status', [AdminController::class, 'updateStatus'])->name('orders.status');
        
        // Tu pourras ajouter ici la gestion des produits plus tard
        // Route::resource('products', AdminProductController::class);
    });
});