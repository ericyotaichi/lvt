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
            $products = Product::where('status', 'published')
                ->orderBy('sort')
                ->orderByDesc('id')
                ->get()
                ->map(function ($product) use ($locale) {
                    return [
                        'id' => $product->id,
                        'title' => ($locale === 'en' && !empty($product->title_en)) ? $product->title_en : $product->title,
                        'slug' => $product->slug,
                        'summary' => ($locale === 'en' && !empty($product->summary_en)) ? $product->summary_en : $product->summary,
                        'content' => ($locale === 'en' && !empty($product->content_en)) ? $product->content_en : $product->content,
                        'cover_url' => $product->cover_url,
                    ];
                })
                ->values()
                ->toArray();

            return Inertia::render('Products/Index', [
                'items' => $products,
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Products/Index', [
                'items' => [],
            ]);
        }
    }

    public function show(string $slug)
    {
        try {
            $product = Product::query()
                ->where('slug', $slug)
                ->where('status', 'published')
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
                    'content'    => ($locale === 'en' && !empty($product->content_en)) ? $product->content_en : $product->content,
                    'cover_url'  => $product->cover_url,
                    'category'   => $product->category,
                ],
            ]);
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}
