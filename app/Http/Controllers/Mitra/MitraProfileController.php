<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MitraProfileController extends Controller
{
    /**
     * Dapatkan merchant aktif milik pengguna yang sedang login.
     */
    private function getActiveMerchant(): ?Merchant
    {
        $merchant = Auth::guard('mitra')->user();
        if ($merchant instanceof Merchant) {
            $merchant->loadCount(['orders', 'reviews'])->load(['orders.items.food']);
            return $merchant;
        }

        $user = Auth::guard('web')->user();
        if ($user && $user->role === 'mitra' && $user->merchant) {
            return $user->merchant->loadCount(['orders', 'reviews'])->load(['orders.items.food']);
        }

        return Merchant::withCount(['orders', 'reviews'])->with(['orders.items.food'])->first();
    }

    /**
     * Tampilkan informasi profil toko & operasional outlet mitra UMKM.
     */
    public function index(): Response|\Illuminate\Http\RedirectResponse
    {
        $merchant = $this->getActiveMerchant();
        if (!$merchant) {
            return redirect()->route('mitra.login');
        }

        // Hitung akumulasi statistik performa dari pesanan selesai
        $completedOrders = $merchant ? $merchant->orders()->where('status', 'selesai')->get() : collect();
        $portionsFromOrders = $completedOrders->sum(function ($order) {
            return $order->items ? $order->items->sum('quantity') : 1;
        });

        $revenueFromOrders = $completedOrders->sum('total_price');

        $basePortions = ($merchant && $merchant->id === 1) ? 248 : 0;
        $totalPortions = $basePortions + $portionsFromOrders;
        $baseRevenue = ($merchant && $merchant->id === 1) ? 3400000 : 0;
        $totalRevenue = $merchant && $merchant->total_revenue_rp > 0
            ? $merchant->total_revenue_rp + $revenueFromOrders
            : $baseRevenue + $revenueFromOrders;

        $co2Kg = round($totalPortions * 0.42, 0);

        return Inertia::render('Mitra/Profil', [
            'merchant' => $merchant,
            'user' => [
                'name' => $merchant->owner_name ?: $merchant->name,
                'email' => $merchant->email,
                'phone' => $merchant->phone,
            ],
            'stats' => [
                'portions_rescued' => $totalPortions,
                'recovered_capital' => $totalRevenue,
                'co2_prevented' => $co2Kg,
                'reviews_count' => $merchant?->reviews_count ?? 0,
                'rating' => $merchant ? (float) $merchant->rating : 5.0,
            ],
        ]);
    }

    /**
     * Perbarui data profil gerai mitra ke database MySQL (tabel merchants).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'storeName' => 'required|string|max:100',
            'ownerName' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'nib' => 'nullable|string|max:50',
            'campusZone' => 'nullable|string|max:100',
            'canteenLocation' => 'nullable|string|max:150',
            'bankName' => 'nullable|string|max:50',
            'bankAccount' => 'nullable|string|max:50',
            'bankHolder' => 'nullable|string|max:100',
        ]);

        $merchant = $this->getActiveMerchant();
        if (!$merchant) {
            return redirect()->route('mitra.login');
        }

        $merchant->update([
            'name' => $validated['storeName'],
            'owner_name' => $validated['ownerName'] ?? $merchant->owner_name,
            'phone' => $validated['phone'] ?? $merchant->phone,
            'email' => !empty($validated['email']) ? strtolower($validated['email']) : $merchant->email,
            'nib' => $validated['nib'] ?? $merchant->nib,
            'campus_zone' => $validated['campusZone'] ?? $merchant->campus_zone,
            'canteen_location' => $validated['canteenLocation'] ?? $merchant->canteen_location,
            'bank_name' => $validated['bankName'] ?? $merchant->bank_name,
            'bank_account' => $validated['bankAccount'] ?? $merchant->bank_account,
            'bank_holder' => $validated['bankHolder'] ?? $merchant->bank_holder,
        ]);

        return back()->with('success', 'Informasi gerai UMKM berhasil diperbarui dan tersimpan di database!');
    }
}
