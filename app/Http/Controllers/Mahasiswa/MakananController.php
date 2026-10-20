<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class MakananController extends Controller
{
    /**
     * Tampilkan detail produk makanan surplus berdasarkan slug.
     */
    public function show(string $slug = 'nasi-ayam-bakar'): Response
    {
        return Inertia::render('Mahasiswa/DetailProduk', [
            'slug' => $slug,
        ]);
    }
}
