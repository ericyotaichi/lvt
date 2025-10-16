<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class AboutController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            $aboutData = AboutPage::getContent($locale);
            
            return Inertia::render('About', [
                'about' => $aboutData,
            ]);
        } catch (\Throwable $e) {
            Log::error("Error fetching about page data: " . $e->getMessage());
            return Inertia::render('About', [
                'about' => null,
            ]);
        }
    }
}
