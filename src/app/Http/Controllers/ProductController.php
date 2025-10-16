<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            $items = Product::where('status', 'published')
                ->orderBy('sort')
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'name' => ($locale === 'en' && !empty($item->title_en)) ? $item->title_en : $item->title,
                        'slug' => $item->slug,
                        'summary' => ($locale === 'en' && !empty($item->summary_en)) ? $item->summary_en : $item->summary,
                        'cover_url' => $item->cover_url,
                        'sort' => $item->sort,
                    ];
                });

            return Inertia::render('Products/Index', ['items' => $items]);
        } catch (\Throwable $e) {
            return Inertia::render('Products/Index', ['items' => []]);
        }
    }

    public function show(string $slug)
    {
        try {
            $product = Product::query()
                ->where('slug', $slug)
                ->where('status', 'published')
                ->with(['applications' => function ($q) {
                    $q->where('applications.status', 'published')
                      ->orderBy('applications.sort')
                      ->orderByDesc('applications.updated_at');
                }])
                ->firstOrFail();

            $locale = app()->getLocale();
            return Inertia::render('Products/Show', [
                'item' => [
                    'id'         => $product->id,
                    'slug'       => $product->slug,
                    'title'      => ($locale === 'en' && !empty($product->title_en)) ? $product->title_en : $product->title,
                    'name'       => ($locale === 'en' && !empty($product->title_en)) ? $product->title_en : $product->title,
                    'summary'    => ($locale === 'en' && !empty($product->summary_en)) ? $product->summary_en : $product->summary,
                    'description' => ($locale === 'en' && !empty($product->description_en)) ? $product->description_en : $product->description,
                    'content'    => $product->content,
                    'cover_url'  => $product->cover_url,
                ],
                'relatedApplications' => $product->applications
                    ->map(function($a) use ($locale) {
                        return [
                            'id'       => $a->id,
                            'slug'     => $a->slug,
                            'title'    => ($locale === 'en' && !empty($a->title_en)) ? $a->title_en : $a->title,
                            'excerpt'  => ($locale === 'en' && !empty($a->excerpt_en)) ? $a->excerpt_en : $a->excerpt,
                            'cover_url' => $a->cover_url,
                        ];
                    })->values(),
            ]);
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}
