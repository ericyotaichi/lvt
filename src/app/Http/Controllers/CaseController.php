<?php
namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\CaseStudy;

class CaseController extends Controller
{
    public function index()
    {
        try {
            $locale = app()->getLocale();
            $items = CaseStudy::where('status','published')
                ->orderBy('id','desc')
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'title' => ($locale === 'en' && !empty($item->title_en)) ? $item->title_en : $item->title,
                        'slug' => $item->slug,
                        'excerpt' => ($locale === 'en' && !empty($item->excerpt_en)) ? $item->excerpt_en : $item->excerpt,
                        'cover_url' => $item->cover_url,
                        'customer' => ($locale === 'en' && !empty($item->customer_en)) ? $item->customer_en : $item->customer,
                    ];
                });
            return Inertia::render('Cases/Index', ['items' => $items]);
        } catch (\Throwable $e) {
            return Inertia::render('Cases/Index', ['items' => []]);
        }
    }

    public function show(string $slug)
    {
        try {
            $case = CaseStudy::query()
                ->where('slug', $slug)
                ->where('status', 'published')
                ->with([
                    'applications' => fn($q) => $q->where('applications.status', 'published')
                                                  ->orderBy('applications.sort')
                                                  ->orderByDesc('applications.updated_at'),
                ])
                ->firstOrFail();

            $locale = app()->getLocale();
            return Inertia::render('Cases/Show', [
                'item' => [
                    'id'       => $case->id,
                    'slug'     => $case->slug,
                    'title'    => ($locale === 'en' && !empty($case->title_en)) ? $case->title_en : $case->title,
                    'excerpt'  => ($locale === 'en' && !empty($case->excerpt_en)) ? $case->excerpt_en : $case->excerpt,
                    'content'  => ($locale === 'en' && !empty($case->content_en)) ? $case->content_en : $case->content,
                    'cover_url' => $case->cover_url,
                    'customer' => ($locale === 'en' && !empty($case->customer_en)) ? $case->customer_en : $case->customer,
                ],
                'relatedApplications' => $case->applications->map(function($a) use ($locale) {
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