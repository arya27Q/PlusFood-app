<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class MitraAuthController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran akun Mitra UMKM baru.
     */
    public function createRegister(): Response|RedirectResponse
    {
        if (Auth::guard('mitra')->check()) {
            return redirect()->route('mitra.dashboard');
        }

        return Inertia::render('Mitra/Register');
    }

    /**
     * Proses pendaftaran akun Mitra UMKM baru dengan pemisahan database tabel merchants.
     */
    public function storeRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'storeName' => 'required|string|max:100',
            'email' => 'required|string|email|max:255',
            'password' => ['required', 'string', 'min:6'],
        ], [
            'storeName.required' => 'Nama toko wajib diisi.',
            'email.required' => 'Email bisnis / toko wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $email = strtolower($validated['email']);

        // Pastikan tidak tabrakan dengan akun Mahasiswa di tabel users
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terdaftar sebagai akun Mahasiswa. Akun Mitra UMKM dan Mahasiswa harus menggunakan email berbeda.',
            ]);
        }

        // Pastikan tidak tabrakan dengan akun Mitra lain di tabel merchants
        if (Merchant::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email toko ini sudah terdaftar sebagai akun Mitra. Silakan masuk ke akun Anda.',
            ]);
        }

        // Buat slug toko unik
        $slug = Str::slug($validated['storeName']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Merchant::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }

        // Simpan langsung dan murni ke tabel merchants (terpisah 100% dari tabel users)
        $merchant = Merchant::create([
            'name' => $validated['storeName'],
            'owner_name' => $validated['storeName'],
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'slug' => $uniqueSlug,
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Kantin Kampus',
            'address' => 'Area Kantin Kampus UNESA Ketintang, Surabaya',
            'canteen_location' => 'Stand Kantin Baru',
            'rating' => 4.8,
            'reviews_count' => 0,
            'is_verified' => true,
            'operating_hours' => '17.00 – 21.00 WIB',
            'default_session' => 'malam',
            'support_eco' => true,
            'balance_rp' => 0,
            'total_revenue_rp' => 0,
        ]);

        return redirect()->route('mitra.login')->with('status', 'Pendaftaran toko mitra berhasil! Silakan masuk dengan email dan kata sandi Anda.');
    }

    /**
     * Tampilkan formulir masuk akun Mitra UMKM.
     */
    public function createLogin(): Response|RedirectResponse
    {
        if (Auth::guard('mitra')->check()) {
            return redirect()->route('mitra.dashboard');
        }

        return Inertia::render('Mitra/Login');
    }

    /**
     * Proses autentikasi masuk akun Mitra UMKM menggunakan guard mitra (tabel merchants).
     */
    public function storeLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email toko wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $email = strtolower($credentials['email']);

        // Jika terdaftar sebagai akun Mahasiswa di tabel users
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Akun ini terdaftar sebagai Mahasiswa, bukan Mitra UMKM. Silakan masuk lewat portal Mahasiswa.',
            ]);
        }

        $merchant = Merchant::where('email', $email)->first();

        if (! $merchant) {
            throw ValidationException::withMessages([
                'email' => 'Email toko belum terdaftar. Silakan daftar toko mitra terlebih dahulu.',
            ]);
        }

        if (! Auth::guard('mitra')->attempt(['email' => $email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi toko salah. Silakan coba lagi.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route('mitra.dashboard');
    }

    /**
     * Keluar dari sesi akun Mitra UMKM.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('mitra')->logout();

        return redirect()->route('mitra.login')->with('status', 'Anda telah keluar dari Portal Mitra UMKM.');
    }
}
