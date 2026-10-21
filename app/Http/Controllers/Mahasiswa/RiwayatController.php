<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
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
        $query = Order::with(['merchant', 'items.food', 'payment'])->latest();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $orders = $query->get();

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
        $query = Order::with(['merchant', 'items.food', 'payment'])->latest();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $orders = $query->get();

        return Inertia::render('Mahasiswa/Riwayat', [
            'initialTicketId' => $id,
            'dbOrders' => $orders,
        ]);
    }
}
