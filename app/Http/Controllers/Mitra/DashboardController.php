<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard ringkasan statistik & pesanan aktif Mitra UMKM.
     */
    public function index(): Response
    {
        return Inertia::render('Mitra/Dashboard');
    }
}
