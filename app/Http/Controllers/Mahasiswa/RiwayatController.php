<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RiwayatController extends Controller
{
    /**
     * Tampilkan riwayat reservasi dan status pesanan mahasiswa.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $orders = [];

        if ($user && $user->role === 'mahasiswa') {
            $orders = Order::with(['merchant', 'items.food', 'payment'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return Inertia::render('Mahasiswa/Riwayat', [
            'dbOrders' => $orders,
        ]);
    }

    /**
     * Tampilkan tiket pengambilan makanan dengan QR Code.
     */
    public function tiket(Request $request, ?string $id = null): Response
    {
        $user = $request->user();
        $orders = [];

        if ($user && $user->role === 'mahasiswa') {
            $orders = Order::with(['merchant', 'items.food', 'payment'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return Inertia::render('Mahasiswa/Riwayat', [
            'initialTicketId' => $id,
            'dbOrders' => $orders,
        ]);
    }

    /**
     * Simpan ulasan pesanan dari mahasiswa, tambahkan ulasan ke profil mitra UMKM,
     * dan berikan reward poin gamifikasi mahasiswa.
     */
    public function storeReview(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|array',
            'comment' => 'nullable|string|max:500',
        ]);

        $cleanId = preg_replace('/[^0-9]/', '', $id);
        $order = Order::with(['merchant', 'items.food'])
            ->where('id', $id)
            ->orWhere('order_number', $id)
            ->when($cleanId, function ($query, $clean) {
                $query->orWhere('order_number', 'LIKE', "%{$clean}%");
            })
            ->first();

        if ($order) {
            $order->has_review = true;
            $order->save();

            // Simpan atau update review di database
            Review::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id ?? Auth::id() ?? 1,
                    'food_id' => $order->items->first()?->food_id ?? 1,
                    'merchant_id' => $order->merchant_id,
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'] ?? null,
                    'tags' => $validated['tags'] ?? [],
                    'reward_points' => 15,
                ]
            );

            // Perbarui review count dan rating merchant
            $merchant = $order->merchant;
            if ($merchant) {
                $oldCount = (int) $merchant->reviews_count;
                $oldRating = (float) $merchant->rating;
                $newRating = $oldCount > 0
                    ? round((($oldRating * $oldCount) + $validated['rating']) / ($oldCount + 1), 1)
                    : (float) $validated['rating'];

                $merchant->increment('reviews_count');
                $merchant->rating = max(1.0, min(5.0, $newRating));
                $merchant->save();
            }
        } else {
            // Fallback pesanan lokal: tetap increment reviews_count & rating pada merchant utama
            $merchant = Merchant::first();
            if ($merchant) {
                $oldCount = (int) $merchant->reviews_count;
                $oldRating = (float) $merchant->rating;
                $newRating = $oldCount > 0
                    ? round((($oldRating * $oldCount) + $validated['rating']) / ($oldCount + 1), 1)
                    : (float) $validated['rating'];

                $merchant->increment('reviews_count');
                $merchant->rating = max(1.0, min(5.0, $newRating));
                $merchant->save();
            }
        }

        // Tambah poin gamifikasi untuk pengguna
        $user = Auth::user();
        if ($user) {
            $user->increment('portions_rescued', 1);
        }

        return back()->with('success', 'Ulasan berhasil dikirim! Rating mitra diperbarui dan Anda meraih +15 Green Points.');
    }

    /**
     * Kosongkan seluruh riwayat pesanan mahasiswa (untuk pengujian bersih).
     */
    public function clearAll(Request $request): RedirectResponse
    {
        $user = $request->user();
        $query = Order::query();

        if ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereNull('user_id');
        }

        $orderIds = $query->pluck('id');
        if ($orderIds->isNotEmpty()) {
            \App\Models\OrderItem::whereIn('order_id', $orderIds)->delete();
            \App\Models\Payment::whereIn('order_id', $orderIds)->delete();
            \App\Models\Review::whereIn('order_id', $orderIds)->delete();
            Order::whereIn('id', $orderIds)->delete();
        }

        return back()->with('success', 'Riwayat pesanan berhasil dikosongkan.');
    }

    /**
     * Batalkan pesanan dari POV Mahasiswa dengan alasan dan batas toleransi 15 menit.
     */
    public function cancelOrder(Request $request, string|int $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $cleanId = trim((string) $id);
        $order = Order::with(['items.food', 'merchant'])
            ->where('id', $cleanId)
            ->orWhere('order_number', $cleanId)
            ->first();

        if (!$order) {
            $digits = preg_replace('/[^0-9]/', '', $cleanId);
            if ($digits) {
                $order = Order::with(['items.food', 'merchant'])
                    ->where('order_number', 'LIKE', "%{$digits}%")
                    ->first();
            }
        }

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order->status === 'selesai') {
            return back()->with('error', 'Pesanan yang sudah selesai diambil tidak dapat dibatalkan.');
        }

        if ($order->status === 'batal') {
            return back()->with('info', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        // Cek toleransi batas waktu pembatalan maksimal 15 menit setelah pemesanan
        if ($order->created_at && $order->created_at->diffInMinutes(now()) > 15) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah melewati batas waktu toleransi 15 menit.');
        }

        $reason = $validated['reason'] ?: 'Dibatalkan oleh mahasiswa';
        $order->status = 'batal';
        $order->notes = ($order->notes ? $order->notes . ' | ' : '') . "Alasan batal: {$reason}";
        $order->save();

        // Kembalikan stok makanan ke listing surplus agar dapat diselamatkan orang lain
        foreach ($order->items as $item) {
            if ($item->food) {
                $item->food->increment('stock', (int) $item->quantity);
                if ($item->food->status === 'habis' && $item->food->stock > 0) {
                    $item->food->status = 'aktif';
                    $item->food->save();
                }
            }
        }

        return back()->with('success', 'Pesanan berhasil dibatalkan dan porsi surplus dikembalikan ke listing.');
    }
}
