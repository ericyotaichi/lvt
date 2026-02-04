<?php

namespace App\Http\Controllers;

use App\Models\TechPage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class TechController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            $techData = TechPage::getContent($locale);
            
            return Inertia::render('Tech', [
                'tech' => $techData,
            ]);
        } catch (\Throwable $e) {
            Log::error("Error fetching tech page data: " . $e->getMessage());
            return Inertia::render('Tech', [
                'tech' => null,
            ]);
        }
    }
}

