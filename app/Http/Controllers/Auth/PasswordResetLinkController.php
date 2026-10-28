<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset verification and update request (Tanpa SMTP).
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required' => 'Email terdaftar wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'name.required' => 'Nama lengkap terdaftar wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $email = strtolower($request->email);
        $user = User::where('email', $email)->where('role', 'mahasiswa')->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email belum terdaftar sebagai akun Mahasiswa.',
            ]);
        }

        $inputName = trim(strtolower($request->name));
        $userName = trim(strtolower($user->name));

        if ($userName !== $inputName) {
            throw ValidationException::withMessages([
                'name' => 'Nama lengkap tidak cocok dengan data akun terdaftar.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }
}
