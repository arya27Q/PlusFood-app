<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class KatalogController extends Controller
{
    /**
     * Tampilkan katalog lengkap makanan surplus dengan filter interaktif.
     */
    public function index(): Response
    {
        return Inertia::render('Mahasiswa/Katalog');
    }
}
