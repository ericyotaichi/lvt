<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CarouselSlide;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            // 依你的欄位調整：title / slug / summary / cover_url / status / sort
            $latest = Product::where('status', 'published')
                ->orderBy('sort')          // 先依 sort
                ->orderByDesc('id')        // 再依 id 新到舊
                ->take(3)
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'title' => ($locale === 'en' && !empty($item->title_en)) ? $item->title_en : $item->title,
                        'slug' => $item->slug,
                        'summary' => ($locale === 'en' && !empty($item->summary_en)) ? $item->summary_en : $item->summary,
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

            return Inertia::render('Home', [
                'latestProducts' => $latest,
                'carouselSlides' => $carouselSlides,
            ]);
        } catch (QueryException $e) {
            Log::error("Error fetching products for home page: " . $e->getMessage());
            return Inertia::render('Home', [
                'latestProducts' => [],
                'carouselSlides' => [],
            ]);
        } catch (\Throwable $e) {
            // 如果数据库连接失败，返回空的产品列表
            return Inertia::render('Home', [
                'latestProducts' => [],
                'carouselSlides' => [],
            ]);
        }
    }
}
