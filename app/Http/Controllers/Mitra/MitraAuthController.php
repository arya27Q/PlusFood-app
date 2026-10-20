<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class MitraAuthController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran akun Mitra UMKM baru.
     */
    public function createRegister(): Response
    {
        return Inertia::render('Mitra/Register');
    }

    /**
     * Tampilkan formulir masuk akun Mitra UMKM.
     */
    public function createLogin(): Response
    {
        return Inertia::render('Mitra/Login');
    }
}
