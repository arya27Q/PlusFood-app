<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dapatkan data merchant aktif milik akun mitra yang sedang login.
     */
    private function getActiveMerchant(): ?Merchant
    {
        $merchant = Auth::guard('mitra')->user();
        if ($merchant instanceof Merchant) {
            return $merchant;
        }

        $user = Auth::guard('web')->user();
        if ($user && $user->role === 'mitra' && $user->merchant) {
            return $user->merchant;
        }

        return Merchant::whereNotNull('email')->first() ?? Merchant::first();
    }

    /**
     * Tampilkan dashboard ringkasan statistik & pesanan aktif Mitra UMKM dari database MySQL.
     */
    public function index(): Response|\Illuminate\Http\RedirectResponse
    {
        $merchant = $this->getActiveMerchant();
        if (!$merchant) {
            return redirect()->route('mitra.login');
        }

        // Ambil data pesanan riil dari database MySQL khusus merchant ini
        $orders = Order::with(['items.food', 'payment', 'user', 'merchant'])
            ->where('merchant_id', $merchant->id)
            ->latest()
            ->get();

        // Kalkulasi metrik analitik dashboard riil
        $completedOrders = $orders->where('status', 'selesai');
        $pendingOrders = $orders->whereIn('status', ['menunggu', 'siap_ambil']);

        $realRevenue = (int) $completedOrders->sum('total_price');
        $todayRevenue = $realRevenue > 0 ? $realRevenue : (int) ($merchant->balance_rp ?: 0);

        $realRescued = (int) $completedOrders->sum(function ($order) {
            return $order->items ? $order->items->sum('quantity') : 1;
        });
        $rescuedPortions = $realRescued;

        $activeListingStock = (int) Food::where('merchant_id', $merchant->id)->sum('stock');
        $initialListingStock = (int) Food::where('merchant_id', $merchant->id)->sum('stock_initial');
        $totalListingPortions = $initialListingStock > 0 ? $initialListingStock : $activeListingStock;
        $recoveryRate = $totalListingPortions > 0 
            ? min(100, (int) round(($rescuedPortions / $totalListingPortions) * 100)) 
            : 0;

        $metrics = [
            'todayRevenue' => $todayRevenue,
            'availableBalance' => $todayRevenue,
            'rescuedPortions' => $rescuedPortions,
            'totalListingPortions' => $totalListingPortions,
            'recoveryRate' => $recoveryRate,
            'pendingCount' => $pendingOrders->count(),
            'completedCount' => $completedOrders->count(),
            'totalOrders' => $orders->count(),
        ];

        // Persiapkan data harian 7 hari terakhir (Live 7 Hari) riil dari database
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $dayShorts = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $chartDaily = [];
        $currentDate = now()->subDays(6);
        $defaultListingCount = max(1, $totalListingPortions);

        for ($i = 0; $i < 7; $i++) {
            $date = $currentDate->copy()->addDays($i);
            $dayOfWeek = (int) $date->format('w');
            $dayName = $dayNames[$dayOfWeek];
            $dayShort = $dayShorts[$dayOfWeek];
            $formattedDate = $date->format('j') . ' ' . $monthNames[(int) $date->format('n')];
            $isToday = $date->isToday();

            // Filter pesanan yang selesai pada hari tersebut
            $dayOrders = $orders->filter(function ($order) use ($date) {
                $orderDate = $order->completed_at ? \Carbon\Carbon::parse($order->completed_at) : ($order->created_at ? \Carbon\Carbon::parse($order->created_at) : null);
                return $orderDate && $orderDate->isSameDay($date);
            });

            $completedDayOrders = $dayOrders->where('status', 'selesai');

            $dayPorsi = (int) $completedDayOrders->sum(function ($o) {
                return $o->items ? $o->items->sum('quantity') : 1;
            });
            $dayRevenue = (int) $completedDayOrders->sum('total_price');
            $dayCostSaved = (int) $completedDayOrders->sum('money_saved');
            $dayWeightGrams = (int) $completedDayOrders->sum('weight_saved_grams');
            $dayCo2 = round($dayWeightGrams > 0 ? ($dayWeightGrams / 1000 * 2.5) : ($dayPorsi * 0.25), 1);
            $dayListing = max($dayPorsi, $defaultListingCount);

            $chartDaily[] = [
                'day' => $isToday ? "{$dayName} (Hari Ini)" : $dayName,
                'short' => $dayShort,
                'date' => $formattedDate,
                'porsi' => $dayPorsi,
                'totalListing' => $dayListing,
                'revenue' => $dayRevenue,
                'costSaved' => $dayCostSaved,
                'co2' => $dayCo2,
                'peakHour' => $completedDayOrders->first()?->pickup_time ?: '19.30 WIB',
                'isToday' => $isToday,
            ];
        }

        $ecoOrdersCount = $orders->filter(function ($o) {
            return $o->items && $o->items->contains('eco_packaging', 'bawa_sendiri');
        })->count();
        $ecoPercent = $orders->count() > 0 
            ? (int) round(($ecoOrdersCount / $orders->count()) * 100) 
            : 0;

        return Inertia::render('Mitra/Dashboard', [
            'orders' => $orders,
            'merchant' => $merchant,
            'metrics' => $metrics,
            'chartDaily' => $chartDaily,
            'ecoPercent' => $ecoPercent,
        ]);
    }
}
