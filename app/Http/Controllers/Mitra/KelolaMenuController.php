<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class KelolaMenuController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan inventaris makanan surplus mitra.
     */
    public function index(): Response
    {
        return Inertia::render('Mitra/KelolaMenu');
    }
}
