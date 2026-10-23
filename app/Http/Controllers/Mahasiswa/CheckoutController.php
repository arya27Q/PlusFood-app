<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman formulir checkout reservasi makanan surplus.
     */
    public function index(Request $request): Response
    {
        $foodQuery = Food::with('merchant')->active();

        if ($request->filled('slug')) {
            $food = (clone $foodQuery)->where('slug', $request->slug)->first();
        }

        if (empty($food)) {
            $food = $foodQuery->first() ?? Food::with('merchant')->first();
        }

        $user = $request->user();
        $qty = max(1, min((int) $request->input('qty', 1), $food ? $food->stock : 1));
        $packaging = $request->input('packaging', 'bawa_sendiri');

        return Inertia::render('Mahasiswa/Checkout', [
            'food' => $food,
            'initialQuantity' => $qty,
            'initialPackaging' => $packaging,
            'customer' => [
                'name' => $user ? $user->name : 'Raka Pratama',
                'email' => $user ? $user->email : 'raka.pratama@mhs.unesa.ac.id',
                'phone' => $user ? ($user->phone ?? '081234567890') : '081234567890',
                'address' => $user ? ($user->address ?? 'Jl. Ketintang Baru No. 14, Gayungan, Surabaya') : 'Jl. Ketintang Baru No. 14, Gayungan, Surabaya',
                'nim' => $user ? ($user->nim ?? '23051204088') : '23051204088',
            ],
        ]);
    }

    /**
     * Simpan reservasi pesanan baru dan buat transaksi pembayaran sandbox.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
            'eco_packaging' => 'required|in:bawa_sendiri,kotak_kertas,thinwall',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_address' => 'required|string|max:500',
            'customer_nim' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:kasir,qris,gopay,dana',
            'arrival_estimate' => 'required|in:awal,tengah,akhir',
            'remind_before' => 'nullable|boolean',
        ], [
            'customer_name.required' => 'Nama lengkap pemesan wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp wajib diisi.',
            'customer_email.required' => 'Alamat email wajib diisi.',
            'customer_address.required' => 'Alamat penjemputan / kos wajib diisi.',
            'payment_method.required' => 'Pilih metode pembayaran.',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            /** @var Food $food */
            $food = Food::with('merchant')->where('id', $validated['food_id'])->lockForUpdate()->firstOrFail();

            if ($food->stock < $validated['quantity']) {
                return back()->withErrors([
                    'quantity' => "Maaf, stok porsi {$food->name} tersisa {$food->stock} porsi.",
                ]);
            }

            // Kurangi stok makanan di database
            $food->decrement('stock', $validated['quantity']);

            // Hitung finansial dan dampak penyelamatan
            $subtotal = $food->price_discount * $validated['quantity'];
            $packagingFee = match ($validated['eco_packaging']) {
                'kotak_kertas' => 1000,
                'thinwall' => 2000,
                default => 0,
            };
            $serviceFee = 1000;
            $totalPrice = $subtotal + $packagingFee + $serviceFee;
            $moneySaved = ($food->price_original - $food->price_discount) * $validated['quantity'];
            $weightSaved = $validated['quantity'] * ($food->weight_grams ?: 400);

            $user = $request->user() ?? User::where('role', 'mahasiswa')->first() ?? User::first();
            $userId = $user ? $user->id : 1;

            $orderNumber = 'PF-' . mt_rand(10000, 99999);
            $merchantInitial = $food->merchant ? $food->merchant->initial : 'PF';
            $qrToken = $orderNumber . '-' . $merchantInitial;

            // 1. Simpan Header Pesanan (tabel orders)
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'merchant_id' => $food->merchant_id,
                'total_items_price' => $subtotal,
                'total_packaging_fee' => $packagingFee,
                'service_fee' => $serviceFee,
                'total_price' => $totalPrice,
                'money_saved' => $moneySaved,
                'weight_saved_grams' => $weightSaved,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'customer_address' => $validated['customer_address'],
                'customer_nim' => $validated['customer_nim'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'pickup_time' => $food->pickup_time_display ?: ($food->pickup_time_start . ' – ' . $food->pickup_time_end . ' WIB'),
                'pickup_deadline' => $food->pickup_deadline_info ?: 'Hari ini',
                'arrival_estimate' => $validated['arrival_estimate'],
                'remind_before' => $validated['remind_before'] ?? true,
                'status' => 'menunggu',
                'qr_token' => $qrToken,
                'has_review' => false,
            ]);

            // 2. Simpan Rincian Item (tabel order_items)
            OrderItem::create([
                'order_id' => $order->id,
                'food_id' => $food->id,
                'food_name' => $food->name,
                'price' => $food->price_discount,
                'quantity' => $validated['quantity'],
                'subtotal' => $subtotal,
                'eco_packaging' => $validated['eco_packaging'],
                'packaging_fee' => $packagingFee,
                'weight_grams' => $weightSaved,
            ]);

            // 3. Simpan Gateway Pembayaran Sandbox Simulator (tabel payments)
            $isCash = $validated['payment_method'] === 'kasir';
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'payment_gateway' => $isCash ? 'cash_on_pickup' : 'sandbox_simulator',
                'payment_reference' => 'SBX-' . strtoupper($validated['payment_method']) . '-' . $orderNumber,
                'amount' => $totalPrice,
                'status' => $isCash ? 'menunggu_bayar' : 'lunas',
                'paid_at' => $isCash ? null : now(),
            ]);

            // 4. Update Gamifikasi & Metrik Penyelamatan Mahasiswa
            if ($user) {
                $user->increment('portions_rescued', $validated['quantity']);
                $user->increment('money_saved_rp', $moneySaved);
                $user->increment('co2_saved_grams', (int) ($weightSaved * 2.5));
            }

            // 5. Update Saldo Omzet Toko Mitra UMKM
            if ($food->merchant) {
                $food->merchant->increment('balance_rp', $subtotal + $packagingFee);
                $food->merchant->increment('total_revenue_rp', $subtotal + $packagingFee);
            }

            return redirect()->route('tiket', ['id' => $order->order_number])
                ->with('success', 'Reservasi berhasil dikonfirmasi! E-Tiket siap ditunjukkan saat pengambilan.');
        });
    }
}
