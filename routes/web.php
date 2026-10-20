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

// 2. Profil Mahasiswa & Edit Profil (Tersambung ke Database MySQL & Preferences)
Route::middleware('auth')->group(function () {
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
Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
Route::get('/tiket/{id?}', [RiwayatController::class, 'tiket'])->name('tiket');

// 4. POV Mitra UMKM (Merchant / Warung & Resto)
Route::prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/', [MitraSplashController::class, 'index'])->name('splash');
    Route::get('/splash', [MitraSplashController::class, 'splash']);
    Route::get('/register', [MitraAuthController::class, 'createRegister'])->name('register');
    Route::get('/login', [MitraAuthController::class, 'createLogin'])->name('login');
    Route::get('/dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelola-menu', [MitraKelolaMenuController::class, 'index'])->name('kelola-menu');
    Route::get('/pesanan', [MitraPesananController::class, 'index'])->name('pesanan');
    Route::get('/profil', [MitraProfileController::class, 'index'])->name('profil');
});

// 5. Fallback Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('beranda');
})->name('dashboard');

require __DIR__.'/auth.php';
