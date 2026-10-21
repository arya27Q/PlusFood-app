<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Inertia\Inertia;
use Inertia\Response;

class BerandaController extends Controller
{
    /**
     * Tampilkan halaman Beranda Mahasiswa dengan rekomendasi surplus terdekat dari database.
     */
    public function index(): Response
    {
        $foods = Food::with('merchant')
            ->active()
            ->latest()
            ->get();

        return Inertia::render('Mahasiswa/Beranda', [
            'foods' => $foods,
        ]);
    }
}
