<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman formulir checkout reservasi makanan surplus.
     */
    public function index(): Response
    {
        return Inertia::render('Mahasiswa/Checkout');
    }
}
