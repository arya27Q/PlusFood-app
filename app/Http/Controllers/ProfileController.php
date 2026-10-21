<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the Mahasiswa profile screen.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();

        // Pastikan preference sudah terisi atau fallback default
        $preference = $user->preference ?: UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'campus_zone' => 'unesa-ketintang',
                'radius' => '2 km',
                'prefer_halal' => true,
                'prefer_vegetarian' => false,
                'prefer_nut_free' => false,
                'prefer_not_spicy' => true,
            ]
        );

        return Inertia::render('Mahasiswa/Profil', [
            'user' => $user,
            'preference' => $preference,
        ]);
    }

    /**
     * Update the user's personal profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Data profil Anda berhasil diperbarui!');
    }

    /**
     * Update the user's dietary & campus zone preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'campus_zone' => 'required|string|max:100',
            'radius' => 'required|string|max:50',
            'prefer_halal' => 'boolean',
            'prefer_vegetarian' => 'boolean',
            'prefer_nut_free' => 'boolean',
            'prefer_not_spicy' => 'boolean',
        ]);

        $user = $request->user();

        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'campus_zone' => $request->campus_zone,
                'radius' => $request->radius,
                'prefer_halal' => (bool) $request->prefer_halal,
                'prefer_vegetarian' => (bool) $request->prefer_vegetarian,
                'prefer_nut_free' => (bool) $request->prefer_nut_free,
                'prefer_not_spicy' => (bool) $request->prefer_not_spicy,
            ]
        );

        return back()->with('success', 'Preferensi makanan & lokasi berhasil disimpan!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password Anda berhasil diperbarui!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
