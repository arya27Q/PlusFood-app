<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PesananController extends Controller
{
    /**
     * Tampilkan antrean reservasi masuk dan scanner validasi QR tiket.
     */
    public function index(): Response
    {
        return Inertia::render('Mitra/Pesanan');
    }
}
