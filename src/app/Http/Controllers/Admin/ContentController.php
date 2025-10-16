<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        // 直接以 product_id 關聯（你的一對一設計）
        $query = DB::table('products as p')
            ->leftJoin('applications as a', 'a.product_id', '=', 'p.id')
            ->leftJoin('cases as c',         'c.product_id', '=', 'p.id')
            ->selectRaw('
                p.id          as product_id,
                p.slug        as slug,
                p.title       as product_title,
                p.status      as product_status,
                p.updated_at  as product_updated_at,

                a.id          as application_id,
                a.title       as application_title,
                a.status      as application_status,
                a.updated_at  as application_updated_at,

                c.id          as case_id,
                c.title       as case_title,
                c.status      as case_status,
                c.updated_at  as case_updated_at,

                GREATEST(
                    COALESCE(p.updated_at, "1970-01-01 00:00:00"),
                    COALESCE(a.updated_at, "1970-01-01 00:00:00"),
                    COALESCE(c.updated_at, "1970-01-01 00:00:00")
                ) as last_updated
            ');

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('p.slug',  'like', $like)
                  ->orWhere('p.title','like', $like)
                  ->orWhere('a.title','like', $like)
                  ->orWhere('c.title','like', $like);
            });
        }

        if (in_array($status, ['draft','published'], true)) {
            // 只顯示產品狀態符合者（如要任一狀態符合就列出，可改為 orWhere 版本）
            $query->where('p.status', $status);
        }

        $items = $query
            ->orderByDesc('last_updated')
            ->paginate(20)
            ->appends($request->query());

        return Inertia::render('Admin/Articles/Index', [
            'filters' => ['q' => $q, 'status' => $status],
            'items'   => $items,
        ]);
    }
}
