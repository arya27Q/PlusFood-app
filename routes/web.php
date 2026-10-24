<?php

use App\Http\Controllers\Mahasiswa\BerandaController;
use App\Http\Controllers\Mahasiswa\CheckoutController;
use App\Http\Controllers\Mahasiswa\KatalogController;
use App\Http\Controllers\Mahasiswa\MakananController;
use App\Http\Controllers\Mahasiswa\RiwayatController;
use App\Http\Controllers\Mitra\DashboardController as MitraDashboardController;
use App\Http\Controllers\Mitra\KelolaMenuController as MitraKelolaMenuController;
use App\Http\Controllers\Mitra\MitraAuthController;
use App\Http\Controllers\Mitra\MitraProfileController;
use App\Http\Controllers\Mitra\MitraSplashController;
use App\Http\Controllers\Mitra\PesananController as MitraPesananController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (PlusFood Structured Controller Architecture)
|--------------------------------------------------------------------------
| Rute web dikelompokkan rapi berdasarkan domain fitur:
| 1. Landing & Splash Screen (WelcomeController)
| 2. Profil Mahasiswa (ProfileController)
| 3. POV Mahasiswa / Konsumen (Mahasiswa\* Controllers)
| 4. POV Mitra UMKM / Merchant (Mitra\* Controllers)
| 5. Autentikasi Mahasiswa (routes/auth.php)
*/

// 1. Landing & Intro Screen
Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/welcome', [WelcomeController::class, 'welcome'])->name('welcome');
Route::get('/loading', [WelcomeController::class, 'loading'])->name('loading');

// 2. Profil Mahasiswa & Edit Profil (Tersambung ke Database MySQL & Preferences - Khusus Mahasiswa)
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/preferences', [ProfileController::class, 'updatePreferences'])->name('profil.preferences');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
});

// 3. POV Mahasiswa (Food Rescue Konsumen)
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/makanan/{slug}', [MakananController::class, 'show'])->name('makanan.detail');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
Route::post('/riwayat/clear', [RiwayatController::class, 'clearAll'])->name('riwayat.clear');
Route::post('/riwayat/{id}/batal', [RiwayatController::class, 'cancelOrder'])->name('riwayat.batal');
Route::post('/riwayat/{id}/ulasan', [RiwayatController::class, 'storeReview'])->name('riwayat.review');
Route::get('/tiket/{id?}', [RiwayatController::class, 'tiket'])->name('tiket');

// 4. POV Mitra UMKM (Merchant / Warung & Resto - Khusus Mitra)
Route::prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/', [MitraSplashController::class, 'index'])->name('splash');
    Route::get('/splash', [MitraSplashController::class, 'splash']);
    Route::get('/register', [MitraAuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [MitraAuthController::class, 'storeRegister'])->name('register.store');
    Route::get('/login', [MitraAuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [MitraAuthController::class, 'storeLogin'])->name('login.store');
    Route::post('/logout', [MitraAuthController::class, 'destroy'])->name('logout');
    Route::middleware(['auth:mitra'])->group(function () {
        Route::get('/dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');
        Route::get('/kelola-menu', [MitraKelolaMenuController::class, 'index'])->name('kelola-menu');
        Route::post('/kelola-menu', [MitraKelolaMenuController::class, 'store'])->name('kelola-menu.store');
        Route::match(['put', 'patch'], '/kelola-menu/{id}', [MitraKelolaMenuController::class, 'update'])->name('kelola-menu.update');
        Route::patch('/kelola-menu/{id}/stock', [MitraKelolaMenuController::class, 'updateStock'])->name('kelola-menu.stock');
        Route::patch('/kelola-menu/{id}/toggle', [MitraKelolaMenuController::class, 'toggleStatus'])->name('kelola-menu.toggle');
        Route::delete('/kelola-menu/{id}', [MitraKelolaMenuController::class, 'destroy'])->name('kelola-menu.destroy');
        Route::get('/pesanan', [MitraPesananController::class, 'index'])->name('pesanan');
        Route::patch('/pesanan/{id}/status', [MitraPesananController::class, 'updateStatus'])->name('pesanan.status');
        Route::get('/profil', [MitraProfileController::class, 'index'])->name('profil');
        Route::match(['put', 'patch', 'post'], '/profil', [MitraProfileController::class, 'update'])->name('profil.update');
    });
});

// 5. Fallback Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('beranda');
})->name('dashboard');

require __DIR__.'/auth.php';
