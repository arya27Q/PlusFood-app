<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes (PlusFood Frontend Prototype)
|--------------------------------------------------------------------------
| Seluruh rute langsung merender komponen Vue via Inertia secara statis/mock.
| Tidak memerlukan backend database authentication atau API controller.
*/

// 1. Landing / Welcome Screen (Pintu Masuk Utama PlusFood)
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/welcome', function () {
    return Inertia::render('Welcome');
})->name('welcome');

// 2. Profil Mahasiswa & Edit Profil (Tersambung ke Database MySQL)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/preferences', [\App\Http\Controllers\ProfileController::class, 'updatePreferences'])->name('profil.preferences');
    Route::put('/profil/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profil.password');
});

// 3. POV Mahasiswa (Konsumen)
Route::get('/beranda', function () {
    return Inertia::render('Mahasiswa/Beranda');
})->name('beranda');

Route::get('/katalog', function () {
    return Inertia::render('Mahasiswa/Katalog');
})->name('katalog');

Route::get('/makanan/{slug}', function ($slug = 'nasi-ayam-bakar') {
    return Inertia::render('Mahasiswa/DetailProduk', [
        'slug' => $slug,
    ]);
})->name('makanan.detail');

Route::get('/checkout', function () {
    return Inertia::render('Mahasiswa/Checkout');
})->name('checkout');

Route::get('/riwayat', function () {
    return Inertia::render('Mahasiswa/Riwayat');
})->name('riwayat');

Route::get('/tiket/{id?}', function ($id = 'PF-88219') {
    return Inertia::render('Mahasiswa/Riwayat', [
        'initialTicketId' => $id,
    ]);
})->name('tiket');

Route::get('/loading', function () {
    return Inertia::render('SplashScreen');
})->name('loading');

// 4. POV Mitra UMKM (Merchant)
Route::get('/mitra', function () {
    return Inertia::render('Mitra/SplashScreen');
})->name('mitra.splash');

Route::get('/mitra/splash', function () {
    return Inertia::render('Mitra/SplashScreen');
});

Route::get('/mitra/register', function () {
    return Inertia::render('Mitra/Register');
})->name('mitra.register');

Route::get('/mitra/login', function () {
    return Inertia::render('Mitra/Login');
})->name('mitra.login');

Route::get('/mitra/dashboard', function () {
    return Inertia::render('Mitra/Dashboard');
})->name('mitra.dashboard');

Route::get('/mitra/kelola-menu', function () {
    return Inertia::render('Mitra/KelolaMenu');
})->name('mitra.kelola-menu');

Route::get('/mitra/pesanan', function () {
    return Inertia::render('Mitra/Pesanan');
})->name('mitra.pesanan');

Route::get('/mitra/profil', function () {
    return Inertia::render('Mitra/Profil');
})->name('mitra.profil');

// 5. Fallback Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('beranda');
})->name('dashboard');

require __DIR__.'/auth.php';

