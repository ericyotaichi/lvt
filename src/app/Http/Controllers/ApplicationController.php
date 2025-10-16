<?php
namespace App\Http\Controllers;

use App\Models\Application;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            $items = Application::where('status','published')
                ->orderBy('id','desc')
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'title' => ($locale === 'en' && !empty($item->title_en)) ? $item->title_en : $item->title,
                        'slug' => $item->slug,
                        'excerpt' => ($locale === 'en' && !empty($item->excerpt_en)) ? $item->excerpt_en : $item->excerpt,
                        'cover_url' => $item->cover_url,
                    ];
                });
            return Inertia::render('Applications/Index', ['items' => $items]);
        } catch (\Throwable $e) {
            return Inertia::render('Applications/Index', ['items' => []]);
        }
    }

    public function show(string $slug)
    {
        try {
            $app = Application::query()
                ->where('slug', $slug)
                ->where('status', 'published')
                ->with([
                    'cases' => fn($q) => $q->where('cases.status', 'published')
                                           ->orderBy('cases.sort')
                                           ->orderByDesc('cases.updated_at'),
                    'products' => fn($q) => $q->where('products.status', 'published')
                                              ->orderBy('products.sort')
                                              ->orderByDesc('products.updated_at'),
                ])
                ->firstOrFail();

            $locale = app()->getLocale();
            return Inertia::render('Applications/Show', [
                'item' => [
                    'id'       => $app->id,
                    'slug'     => $app->slug,
                    'title'    => ($locale === 'en' && !empty($app->title_en)) ? $app->title_en : $app->title,
                    'excerpt'  => ($locale === 'en' && !empty($app->excerpt_en)) ? $app->excerpt_en : $app->excerpt,
                    'content'  => ($locale === 'en' && !empty($app->content_en)) ? $app->content_en : $app->content,
                    'cover_url' => $app->cover_url,
                ],
                'relatedCases' => $app->cases->map(function($c) use ($locale) {
                    return [
                        'id'       => $c->id,
                        'slug'     => $c->slug,
                        'title'    => ($locale === 'en' && !empty($c->title_en)) ? $c->title_en : $c->title,
                        'excerpt'  => ($locale === 'en' && !empty($c->excerpt_en)) ? $c->excerpt_en : $c->excerpt,
                        'cover_url' => $c->cover_url,
                        'customer' => ($locale === 'en' && !empty($c->customer_en)) ? $c->customer_en : $c->customer,
                    ];
                })->values(),
                'relatedProducts' => $app->products->map(function($p) use ($locale) {
                    return [
                        'id'      => $p->id,
                        'slug'    => $p->slug,
                        'title'   => ($locale === 'en' && !empty($p->title_en)) ? $p->title_en : $p->title,
                        'summary' => ($locale === 'en' && !empty($p->summary_en)) ? $p->summary_en : $p->summary,
                        'cover_url' => $p->cover_url,
                    ];
                })->values(),
            ]);
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}
