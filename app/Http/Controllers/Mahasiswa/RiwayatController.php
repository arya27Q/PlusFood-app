<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RiwayatController extends Controller
{
    /**
     * Tampilkan riwayat reservasi dan status pesanan mahasiswa.
     */
    public function index(): Response
    {
        return Inertia::render('Mahasiswa/Riwayat');
    }

    /**
     * Tampilkan tiket pengambilan makanan dengan QR Code.
     */
    public function tiket(?string $id = 'PF-88219'): Response
    {
        return Inertia::render('Mahasiswa/Riwayat', [
            'initialTicketId' => $id,
        ]);
    }
}
