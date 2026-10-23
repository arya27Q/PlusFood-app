<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PesananController extends Controller
{
    /**
     * Dapatkan data merchant aktif (auth atau default Warung Bu Sari).
     */
    private function getActiveMerchant(): Merchant
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && $user->merchant) {
            /** @var Merchant $userMerchant */
            $userMerchant = $user->merchant;
            return $userMerchant;
        }

        /** @var Merchant|null $first */
        $first = Merchant::first();
        if ($first) {
            return $first;
        }

        /** @var Merchant $created */
        $created = Merchant::create([
            'name' => 'Warung Bu Sari',
            'owner_name' => 'Ibu Sari',
            'slug' => 'warung-bu-sari',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Warung Makan',
            'address' => 'Jl. Ketintang Baru No. 12, Wonokromo, Surabaya',
            'canteen_location' => 'Kantin Vokasi Lt. 1 Stand 04',
            'phone' => '081234567890',
            'rating' => 4.8,
            'reviews_count' => 148,
            'balance_rp' => 216000,
            'total_revenue_rp' => 1450000,
        ]);

        return $created;
    }

    /**
     * Tampilkan antrean reservasi masuk dan rekap kasir dari database MySQL.
     */
    public function index(): Response
    {
        $merchant = $this->getActiveMerchant();

        // Query pesanan dari tabel orders MySQL
        $orders = Order::with(['items.food', 'payment', 'user', 'merchant'])
            ->where('merchant_id', $merchant->id)
            ->latest()
            ->get();

        if ($orders->isEmpty()) {
            $allOrders = Order::with(['items.food', 'payment', 'user', 'merchant'])->latest()->get();
            if ($allOrders->isNotEmpty()) {
                $orders = $allOrders;
            }
        }

        $completedOrders = $orders->where('status', 'selesai');
        $pendingOrders = $orders->whereIn('status', ['menunggu', 'siap_ambil']);
        $realRevenue = (int) $completedOrders->sum('total_price');

        $metrics = [
            'totalOrders' => $orders->count(),
            'pendingCount' => $pendingOrders->count(),
            'completedCount' => $completedOrders->count(),
            'ecoBawaSendiriCount' => $orders->filter(function ($o) {
                return $o->items && $o->items->contains('eco_packaging', 'bawa_sendiri');
            })->count(),
            'totalCashierRevenue' => $realRevenue > 0 ? $realRevenue : (int) ($merchant->balance_rp ?: 216000),
        ];

        return Inertia::render('Mitra/Pesanan', [
            'orders' => $orders,
            'merchant' => $merchant,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Perbarui status pesanan (e.g. validasi tiket kasir menjadi 'selesai' atau 'batal').
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:menunggu,siap_ambil,selesai,batal,dibatalkan',
        ]);

        $order = Order::with(['payment', 'merchant', 'items'])
            ->where('id', $id)
            ->orWhere('order_number', $id)
            ->orWhere('qr_token', $id)
            ->firstOrFail();

        $targetStatus = $request->status === 'dibatalkan' ? 'batal' : $request->status;

        if ($targetStatus === 'selesai') {
            $order->completed_at = now();

            // Jika metode pembayaran kasir / belum lunas, tandai lunas seketika
            if ($order->payment && $order->payment->status !== 'lunas') {
                $order->payment->status = 'lunas';
                $order->payment->paid_at = now();
                $order->payment->save();
            }
        }

        $order->status = $targetStatus;
        $order->save();

        return redirect()->back()->with('success', "Status pesanan #{$order->order_number} berhasil diperbarui menjadi {$order->status_label}!");
    }
}
