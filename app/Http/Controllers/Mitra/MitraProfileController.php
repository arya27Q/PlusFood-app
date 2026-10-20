<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class MitraProfileController extends Controller
{
    /**
     * Tampilkan informasi profil toko & operasional outlet mitra UMKM.
     */
    public function index(): Response
    {
        return Inertia::render('Mitra/Profil');
    }
}
