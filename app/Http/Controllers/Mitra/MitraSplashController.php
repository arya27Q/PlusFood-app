<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class MitraSplashController extends Controller
{
    /**
     * Tampilkan splash screen pengenalan portal Mitra UMKM.
     */
    public function index(): Response
    {
        return Inertia::render('Mitra/SplashScreen');
    }

    /**
     * Alias rute splash screen Mitra UMKM.
     */
    public function splash(): Response
    {
        return Inertia::render('Mitra/SplashScreen');
    }
}
