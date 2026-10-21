<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KatalogController extends Controller
{
    /**
     * Tampilkan katalog lengkap makanan surplus dengan filter dan query dari database.
     */
    public function index(Request $request): Response
    {
        $query = Food::with('merchant')->active();

        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        if ($request->filled('category') && $request->input('category') !== 'Semua') {
            $query->category($request->input('category'));
        }

        if ($request->filled('campus') && $request->input('campus') !== 'all') {
            $query->campusZone($request->input('campus'));
        }

        if ($request->filled('max_price')) {
            $query->maxPrice((int) $request->input('max_price'));
        }

        $foods = $query->latest()->get();

        return Inertia::render('Mahasiswa/Katalog', [
            'foods' => $foods,
            'filters' => [
                'q' => $request->input('q', ''),
                'category' => $request->input('category', 'Semua'),
                'campus' => $request->input('campus', 'all'),
                'max_price' => (int) $request->input('max_price', 25000),
            ],
        ]);
    }
}
