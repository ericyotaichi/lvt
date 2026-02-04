<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $query = CaseStudy::query();

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('slug', 'like', $like)
                  ->orWhere('title', 'like', $like)
                  ->orWhere('customer', 'like', $like);
            });
        }

        if (in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }

        $items = $query
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->appends($request->query());

        return Inertia::render('Admin/Cases/Index', [
            'filters' => ['q' => $q, 'status' => $status],
            'items' => $items,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Cases/Form', [
            'mode' => 'create',
            'caseItem' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateForm($request);

        return DB::transaction(function () use ($data, $request) {
            $slug = $data['slug'] ?? null;
            if (!$slug) {
                $slug = Str::slug($data['title']);
                $counter = 1;
                $originalSlug = $slug;
                while (CaseStudy::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $case = new CaseStudy([
                'slug' => $slug,
                'title' => $data['title'],
                'title_en' => $data['title_en'] ?? null,
                'excerpt' => $data['excerpt'] ?? null,
                'excerpt_en' => $data['excerpt_en'] ?? null,
                'content' => $data['content'] ?? null,
                'content_en' => $data['content_en'] ?? null,
                'customer' => $data['customer'] ?? null,
                'customer_en' => $data['customer_en'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'sort' => $data['sort'] ?? 0,
            ]);

            if ($request->hasFile('cover')) {
                $path = $request->file('cover')->store('cases', 'public');
                $case->cover_url = '/storage/' . $path;
            }

            $case->save();

            return redirect()->route('admin.cases.edit', $case->id)
                ->with('success', '已建立案例說明');
        });
    }

    public function edit(CaseStudy $case)
    {
        $case->refresh();

        $caseData = [
            'id' => $case->id,
            'slug' => $case->slug,
            'title' => $case->title,
            'title_en' => $case->title_en,
            'excerpt' => $case->excerpt,
            'excerpt_en' => $case->excerpt_en,
            'content' => $case->content,
            'content_en' => $case->content_en,
            'customer' => $case->customer,
            'customer_en' => $case->customer_en,
            'status' => $case->status,
            'sort' => $case->sort,
            'cover_url' => $case->cover_url,
        ];

        return Inertia::render('Admin/Cases/Form', [
            'mode' => 'edit',
            'caseItem' => $caseData,
        ]);
    }

    public function update(Request $request, CaseStudy $case)
    {
        $data = $this->validateForm($request, $case->id);

        return DB::transaction(function () use ($data, $request, $case) {
            $slug = $data['slug'] ?? null;
            if (!$slug && !$case->slug) {
                $slug = Str::slug($data['title']);
                $counter = 1;
                $originalSlug = $slug;
                while (CaseStudy::where('slug', $slug)->where('id', '!=', $case->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            } elseif (!$slug) {
                $slug = $case->slug;
            }

            $case->fill([
                'slug' => $slug,
                'title' => $data['title'],
                'title_en' => $data['title_en'] ?? null,
                'excerpt' => $data['excerpt'] ?? null,
                'excerpt_en' => $data['excerpt_en'] ?? null,
                'content' => $data['content'] ?? null,
                'content_en' => $data['content_en'] ?? null,
                'customer' => $data['customer'] ?? null,
                'customer_en' => $data['customer_en'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'sort' => $data['sort'] ?? 0,
            ]);

            if ($request->hasFile('cover')) {
                $originalCoverUrl = $case->getOriginal('cover_url');
                if ($originalCoverUrl) {
                    $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $originalCoverUrl);
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $path = $request->file('cover')->store('cases', 'public');
                $case->cover_url = '/storage/' . $path;
            }

            $case->save();

            return back()->with('success', '已更新案例說明');
        });
    }

    public function destroy(CaseStudy $case)
    {
        return DB::transaction(function () use ($case) {
            $originalCoverUrl = $case->getOriginal('cover_url');
            if ($originalCoverUrl) {
                $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $originalCoverUrl);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $case->delete();

            return redirect()->route('admin.cases.index')
                ->with('success', '案例說明已刪除');
        });
    }

    private function validateForm(Request $request, ?int $id = null): array
    {
        $slugRule = $id
            ? "unique:cases,slug,{$id}"
            : 'unique:cases,slug';

        return $request->validate([
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'excerpt_en' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'customer' => ['nullable', 'string', 'max:255'],
            'customer_en' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
            'sort' => ['nullable', 'integer'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ], [
            'slug.unique' => '此 Slug 已被使用，請使用其他 Slug',
            'title.required' => '請輸入案例標題',
            'status.required' => '請選擇狀態',
            'cover.image' => '上傳的檔案必須是圖片',
            'cover.max' => '圖片大小不能超過 4MB',
        ]);
    }
}
