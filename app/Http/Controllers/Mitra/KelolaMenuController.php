<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class KelolaMenuController extends Controller
{
    /**
     * Dapatkan mitra merchant aktif saat ini (auth atau warung utama).
     */
    private function getActiveMerchant(): Merchant
    {
        $user = auth()->user();
        if ($user && $user->merchant) {
            return $user->merchant;
        }

        return Merchant::first() ?? Merchant::create([
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
    }

    /**
     * Tampilkan halaman pengelolaan inventaris makanan surplus mitra.
     */
    public function index(): Response
    {
        $merchant = $this->getActiveMerchant();
        $foods = Food::with('merchant')
            ->where('merchant_id', $merchant->id)
            ->latest()
            ->get();

        return Inertia::render('Mitra/KelolaMenu', [
            'foods' => $foods,
            'merchant' => $merchant,
        ]);
    }

    /**
     * Simpan menu makanan surplus baru ke database MySQL.
     */
    public function store(Request $request): RedirectResponse
    {
        $merchant = $this->getActiveMerchant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'price_original' => 'required|numeric|min:0',
            'price_discount' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'canteen_location' => 'nullable|string|max:255',
            'canteenLocation' => 'nullable|string|max:255',
            'pickup_time_start' => 'nullable|string|max:50',
            'pickup_time_end' => 'nullable|string|max:50',
            'pickupStart' => 'nullable|string|max:50',
            'pickupEnd' => 'nullable|string|max:50',
            'campus_zone_id' => 'nullable|string|max:100',
            'campusZoneId' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'spiciness' => 'nullable|string|max:50',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'kitchen_note' => 'nullable|string',
            'kitchenNote' => 'nullable|string',
            'support_eco_packaging' => 'nullable|boolean',
            'supportEcoPackaging' => 'nullable|boolean',
        ]);

        $orig = (int) $request->price_original;
        $disc = (int) $request->price_discount;
        $discountPercent = $orig > 0 ? (int) round((($orig - $disc) / $orig) * 100) : 0;

        $slug = Str::slug($request->name) . '-' . time();

        $start = $request->pickup_time_start ?? $request->pickupStart ?? '19.00';
        $end = $request->pickup_time_end ?? $request->pickupEnd ?? '20.30';
        $start = str_replace(':', '.', $start);
        $end = str_replace(':', '.', $end);
        $pickupDisplay = "{$start} – {$end} WIB";

        $tags = $request->tags ?? ['Halal', 'Bebas Kacang'];
        $desc = $request->description ?? $request->kitchenNote ?? $request->kitchen_note ?? 'Menu surplus lezat & higienis kualitas prima, disimpan di food warmer berstandar.';

        // Buat record makanan baru di tabel foods
        Food::create([
            'merchant_id' => $merchant->id,
            'name' => trim($request->name),
            'slug' => $slug,
            'category' => $request->category ?? 'Nasi',
            'description' => $desc,
            'price_original' => $orig,
            'price_discount' => $disc,
            'discount_percent' => $discountPercent,
            'stock' => (int) $request->stock,
            'stock_initial' => (int) $request->stock,
            'weight_grams' => 400,
            'pickup_time_start' => $start,
            'pickup_time_end' => $end,
            'pickup_time_display' => $pickupDisplay,
            'pickup_deadline_info' => "Hari ini · tutup {$end} WIB",
            'dietary_tags' => $tags,
            'is_sealed' => true,
            'is_cooked_today' => true,
            'image' => $request->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80',
            'status' => 'aktif',
        ]);

        // Perbarui info titik stand merchant jika diisi
        $canteenLoc = $request->canteenLocation ?? $request->canteen_location;
        if ($canteenLoc && $canteenLoc !== $merchant->canteen_location) {
            $merchant->canteen_location = $canteenLoc;
            $merchant->save();
        }

        return redirect()->back()->with('success', 'Menu surplus berhasil ditambahkan ke database!');
    }

    /**
     * Perbarui jumlah sisa stok makanan surplus.
     */
    public function updateStock(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $food = Food::findOrFail($id);
        $stock = (int) $request->stock;

        $food->stock = $stock;
        $food->status = $stock <= 0 ? 'habis' : 'aktif';
        $food->save();

        return redirect()->back()->with('success', 'Stok makanan berhasil diperbarui!');
    }

    /**
     * Beralih status makanan antara 'aktif' (tayang) dan 'habis'.
     */
    public function toggleStatus($id): RedirectResponse
    {
        $food = Food::findOrFail($id);
        $food->status = $food->status === 'aktif' ? 'habis' : 'aktif';
        $food->save();

        return redirect()->back()->with('success', 'Status menu berhasil diubah!');
    }

    /**
     * Hapus menu surplus dari database.
     */
    public function destroy($id): RedirectResponse
    {
        $food = Food::findOrFail($id);
        $food->delete();

        return redirect()->back()->with('success', 'Menu surplus berhasil dihapus!');
    }
}
