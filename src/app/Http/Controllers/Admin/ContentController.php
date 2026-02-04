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

        // 只查询产品数据（不再包含应用场域和案例）
        $query = DB::table('products as p')
            ->selectRaw('
                p.id          as product_id,
                p.slug        as slug,
                p.title       as product_title,
                p.status      as product_status,
                p.updated_at  as last_updated
            ');

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('p.slug',  'like', $like)
                  ->orWhere('p.title','like', $like);
            });
        }

        if (in_array($status, ['draft','published'], true)) {
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
