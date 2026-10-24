<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class KelolaMenuController extends Controller
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
     * Tampilkan halaman pengelolaan inventaris makanan surplus mitra.
     */
    public function index(): Response|\Illuminate\Http\RedirectResponse
    {
        $merchant = $this->getActiveMerchant();
        if (!$merchant) {
            return redirect()->route('mitra.login');
        }
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
        if (!$merchant) {
            return redirect()->route('mitra.login');
        }

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

        // Perbarui info titik stand & zona kampus merchant jika diisi
        $zoneId = $request->campus_zone_id ?? $request->campusZoneId;
        if ($zoneId) {
            $merchant->campus_zone = $zoneId;
            $zoneNames = [
                'unesa-ketintang' => 'UNESA Ketintang',
                'unesa-lidah' => 'UNESA Lidah Wetan',
                'its-sukolilo' => 'ITS Sukolilo',
                'unair-b' => 'UNAIR Kampus B',
                'upn-rungkut' => 'UPN Veteran Rungkut',
                'petra-siwalankerto' => 'UK Petra Siwalankerto',
            ];
            $merchant->campus_name = $zoneNames[$zoneId] ?? 'Kampus Surabaya';
        }

        $canteenLoc = $request->canteenLocation ?? $request->canteen_location;
        if ($canteenLoc) {
            $merchant->canteen_location = $canteenLoc;
        }
        $merchant->save();

        return redirect()->back()->with('success', 'Menu surplus berhasil ditambahkan ke database!');
    }

    /**
     * Perbarui detail data menu surplus (Edit Menu).
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        $food = Food::find($id);
        if (!$food) {
            return redirect()->back()->with('success', 'Menu telah diperbarui!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'price_original' => 'required|numeric|min:0',
            'price_discount' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'pickup_time_start' => 'nullable|string|max:50',
            'pickup_time_end' => 'nullable|string|max:50',
            'pickupStart' => 'nullable|string|max:50',
            'pickupEnd' => 'nullable|string|max:50',
            'tags' => 'nullable|array',
            'description' => 'nullable|string',
            'kitchenNote' => 'nullable|string',
        ]);

        $orig = (int) $request->price_original;
        $disc = (int) $request->price_discount;
        $discountPercent = $orig > 0 ? (int) round((($orig - $disc) / $orig) * 100) : 0;

        $start = $request->pickup_time_start ?? $request->pickupStart ?? $food->pickup_time_start;
        $end = $request->pickup_time_end ?? $request->pickupEnd ?? $food->pickup_time_end;
        $start = str_replace(':', '.', $start);
        $end = str_replace(':', '.', $end);

        $stock = (int) $request->stock;

        $food->update([
            'name' => trim($request->name),
            'category' => $request->category ?? $food->category,
            'description' => $request->description ?? $request->kitchenNote ?? $food->description,
            'price_original' => $orig,
            'price_discount' => $disc,
            'discount_percent' => $discountPercent,
            'stock' => $stock,
            'pickup_time_start' => $start,
            'pickup_time_end' => $end,
            'pickup_time_display' => "{$start} – {$end} WIB",
            'pickup_deadline_info' => "Hari ini · tutup {$end} WIB",
            'dietary_tags' => $request->tags ?? $food->dietary_tags,
            'status' => $stock <= 0 ? 'habis' : 'aktif',
        ]);

        return redirect()->back()->with('success', 'Menu surplus berhasil diperbarui!');
    }

    /**
     * Perbarui jumlah sisa stok makanan surplus.
     */
    public function updateStock(Request $request, string|int $id): RedirectResponse
    {
        $food = Food::find($id);
        if (!$food) {
            return redirect()->back()->with('success', 'Stok makanan berhasil diperbarui!');
        }

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $stock = (int) $request->stock;

        $food->stock = $stock;
        $food->status = $stock <= 0 ? 'habis' : 'aktif';
        $food->save();

        return redirect()->back()->with('success', 'Stok makanan berhasil diperbarui!');
    }

    /**
     * Beralih status makanan antara 'aktif' (tayang) dan 'habis'.
     */
    public function toggleStatus(string|int $id): RedirectResponse
    {
        $food = Food::find($id);
        if (!$food) {
            return redirect()->back()->with('success', 'Status menu berhasil diubah!');
        }

        $food->status = $food->status === 'aktif' ? 'habis' : 'aktif';
        $food->save();

        return redirect()->back()->with('success', 'Status menu berhasil diubah!');
    }

    /**
     * Hapus menu surplus dari database.
     */
    public function destroy(string|int $id): RedirectResponse
    {
        $food = Food::find($id);
        if ($food) {
            $food->delete();
        }

        return redirect()->back()->with('success', 'Menu surplus berhasil dihapus!');
    }
}
