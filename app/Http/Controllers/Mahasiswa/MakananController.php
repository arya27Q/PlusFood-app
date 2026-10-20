<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Inertia\Inertia;
use Inertia\Response;

class MakananController extends Controller
{
    /**
     * Tampilkan detail produk makanan surplus berdasarkan slug dari database.
     */
    public function show(string $slug = 'nasi-ayam-bakar'): Response
    {
        $food = Food::with('merchant')
            ->where('slug', $slug)
            ->first();

        // Fallback jika slug tidak ditemukan ke makanan aktif pertama
        if (!$food) {
            $food = Food::with('merchant')->active()->firstOrFail();
        }

        return Inertia::render('Mahasiswa/DetailProduk', [
            'slug' => $food->slug,
            'food' => $food,
        ]);
    }
}
