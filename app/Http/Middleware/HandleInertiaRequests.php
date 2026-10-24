<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::guard('web')->user();
        $mitra = Auth::guard('mitra')->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? $user->load('preference') : null,
                'mitra' => $mitra,
                'isMahasiswa' => $user && $user->role === 'mahasiswa',
                'isMitra' => (bool) $mitra,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
                'order_id' => fn () => $request->session()->get('order_id'),
            ],
            'mitraPendingCount' => function () {
                try {
                    $mitra = Auth::guard('mitra')->user();
                    if ($mitra) {
                        return Order::where('merchant_id', $mitra->id)
                            ->whereIn('status', ['menunggu', 'siap_ambil'])
                            ->count();
                    }
                    return 0;
                } catch (\Throwable $e) {
                    return 0;
                }
            },
            'allMitraOrders' => function () {
                try {
                    $mitra = Auth::guard('mitra')->user();
                    if ($mitra) {
                        return Order::with(['items.food', 'payment', 'user', 'merchant'])
                            ->where('merchant_id', $mitra->id)
                            ->latest()
                            ->take(30)
                            ->get();
                    }

                    $user = Auth::guard('web')->user();
                    if ($user && $user->role === 'mahasiswa') {
                        return Order::with(['items.food', 'payment', 'user', 'merchant'])
                            ->where('user_id', $user->id)
                            ->latest()
                            ->take(30)
                            ->get();
                    }

                    return [];
                } catch (\Throwable $e) {
                    return [];
                }
            },
        ];
    }
}
