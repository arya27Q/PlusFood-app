<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Tampilkan landing page utama PlusFood.
     */
    public function index(): Response
    {
        return Inertia::render('Welcome');
    }

    /**
     * Alias landing page welcome.
     */
    public function welcome(): Response
    {
        return Inertia::render('Welcome');
    }

    /**
     * Tampilkan animasi loading screen transisi sebelum masuk ke beranda.
     */
    public function loading(): Response
    {
        return Inertia::render('SplashScreen');
    }
}
