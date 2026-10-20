<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BerandaController extends Controller
{
    /**
     * Tampilkan halaman Beranda Mahasiswa dengan rekomendasi surplus terdekat.
     */
    public function index(): Response
    {
        return Inertia::render('Mahasiswa/Beranda');
    }
}
