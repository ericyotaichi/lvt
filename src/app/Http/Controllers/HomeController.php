<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CarouselSlide;
use App\Models\TechPage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            
            $products = Product::where('status', 'published')
                ->orderBy('sort')
                ->orderByDesc('id')
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'title' => ($locale === 'en' && !empty($item->title_en)) ? $item->title_en : $item->title,
                        'summary' => ($locale === 'en' && !empty($item->summary_en)) ? $item->summary_en : $item->summary,
                        'content' => ($locale === 'en' && !empty($item->content_en)) ? $item->content_en : $item->content,
                        'cover_url' => $item->cover_url,
                    ];
                });

            // 获取轮播数据
            $carouselSlides = [];
            try {
                $carouselSlides = CarouselSlide::where('status', true)
                    ->orderBy('sort')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($slide) use ($locale) {
                        return [
                            'id' => $slide->id,
                            'title' => ($locale === 'en' && !empty($slide->title_en)) ? $slide->title_en : $slide->title,
                            'description' => ($locale === 'en' && !empty($slide->description_en)) ? $slide->description_en : $slide->description,
                            'image_url' => $slide->image_url,
                            'link_url' => $slide->link_url,
                            'link_text' => ($locale === 'en' && !empty($slide->link_text_en)) ? $slide->link_text_en : $slide->link_text,
                        ];
                    })->toArray();
            } catch (QueryException $e) {
                Log::error("Error fetching carousel slides: " . $e->getMessage());
                $carouselSlides = [];
            }

            $techData = TechPage::getContent($locale);
            if ($techData) {
                $techData['excerpt'] = Str::limit(strip_tags($techData['content'] ?? ''), 200);
            }

            return Inertia::render('Home', [
                'products' => $products,
                'carouselSlides' => $carouselSlides,
                'tech' => $techData,
            ]);
        } catch (QueryException $e) {
            Log::error("Error fetching products for home page: " . $e->getMessage());
            return Inertia::render('Home', [
                'products' => [],
                'carouselSlides' => [],
                'tech' => null,
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Home', [
                'products' => [],
                'carouselSlides' => [],
                'tech' => null,
            ]);
        }
    }
}
