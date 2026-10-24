<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BerandaController extends Controller
{
    /**
     * Tampilkan halaman Beranda Mahasiswa dengan rekomendasi surplus terdekat dan pesanan aktif.
     */
    public function index(Request $request): Response
    {
        $foods = Food::with('merchant')
            ->active()
            ->latest()
            ->get();

        $user = $request->user();
        $dbOrders = [];
        if ($user && $user->role === 'mahasiswa') {
            $dbOrders = Order::with(['merchant', 'items.food', 'payment'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(30)
                ->get();
        }

        return Inertia::render('Mahasiswa/Beranda', [
            'foods' => $foods,
            'dbOrders' => $dbOrders,
        ]);
    }
}
