<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $email = strtolower($request->email);

        if (\App\Models\Merchant::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terdaftar sebagai akun Mitra UMKM. Akun Mahasiswa dan Mitra harus menggunakan email berbeda.',
            ]);
        }

        $existing = User::where('email', $email)->first();
        if ($existing) {
            $msg = $existing->role === 'mitra'
                ? 'Email ini sudah terdaftar sebagai akun Mitra UMKM. Akun Mahasiswa dan Mitra harus menggunakan email berbeda.'
                : 'Akun email ini sudah terdaftar sebagai Mahasiswa. Silakan gunakan email lain atau masuk ke akun Anda.';
            throw ValidationException::withMessages([
                'email' => $msg,
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'campus' => 'nullable|string|max:255',
            'nim' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Akun email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'phone' => $request->phone ?: '081234567890',
            'campus' => $request->campus ?: 'Universitas Negeri Surabaya',
            'nim' => $request->nim ?: null,
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            'co2_saved_grams' => 0,
            'money_saved_rp' => 0,
            'portions_rescued' => 0,
            'rescuer_level' => 1,
        ]);

        \App\Models\UserPreference::create([
            'user_id' => $user->id,
            'campus_zone' => 'unesa-ketintang',
            'radius' => '2 km',
            'prefer_halal' => true,
            'prefer_vegetarian' => false,
            'prefer_nut_free' => false,
            'prefer_not_spicy' => true,
        ]);

        event(new Registered($user));

        return redirect()
    ->route('login')
    ->with('status', 'Pendaftaran berhasil. Silakan masuk dengan akun baru Anda.');
    }
}
