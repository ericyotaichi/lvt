<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $query = Application::query()
            ->select('applications.*', 'products.title as product_title')
            ->leftJoin('products', 'products.id', '=', 'applications.product_id');

        if ($q !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
            $query->where(function ($w) use ($like) {
                $w->where('applications.slug', 'like', $like)
                  ->orWhere('applications.title', 'like', $like);
            });
        }

        if (in_array($status, ['draft', 'published'], true)) {
            $query->where('applications.status', $status);
        }

        $items = $query
            ->orderByDesc('applications.updated_at')
            ->paginate(20)
            ->appends($request->query());

        return Inertia::render('Admin/Applications/Index', [
            'filters' => ['q' => $q, 'status' => $status],
            'items' => $items,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Applications/Form', [
            'mode' => 'create',
            'application' => null,
            'products' => $this->getProductOptions(),
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
                while (Application::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $application = new Application([
                'slug' => $slug,
                'title' => $data['title'],
                'title_en' => $data['title_en'] ?? null,
                'excerpt' => $data['excerpt'] ?? null,
                'excerpt_en' => $data['excerpt_en'] ?? null,
                'content' => $data['content'] ?? null,
                'content_en' => $data['content_en'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'sort' => $data['sort'] ?? 0,
            ]);

            if ($request->hasFile('cover')) {
                $path = $request->file('cover')->store('applications', 'public');
                $application->cover_url = '/storage/' . $path;
            }

            $application->save();

            return redirect()->route('admin.applications.edit', $application->id)
                ->with('success', '已建立應用場域');
        });
    }

    public function edit(Application $application)
    {
        $application->refresh();

        $applicationData = [
            'id' => $application->id,
            'slug' => $application->slug,
            'title' => $application->title,
            'title_en' => $application->title_en,
            'excerpt' => $application->excerpt,
            'excerpt_en' => $application->excerpt_en,
            'content' => $application->content,
            'content_en' => $application->content_en,
            'product_id' => $application->product_id,
            'status' => $application->status,
            'sort' => $application->sort,
            'cover_url' => $application->cover_url,
        ];

        return Inertia::render('Admin/Applications/Form', [
            'mode' => 'edit',
            'application' => $applicationData,
            'products' => $this->getProductOptions(),
        ]);
    }

    public function update(Request $request, Application $application)
    {
        $data = $this->validateForm($request, $application->id);

        return DB::transaction(function () use ($data, $request, $application) {
            $slug = $data['slug'] ?? null;
            if (!$slug && !$application->slug) {
                $slug = Str::slug($data['title']);
                $counter = 1;
                $originalSlug = $slug;
                while (Application::where('slug', $slug)->where('id', '!=', $application->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            } elseif (!$slug) {
                $slug = $application->slug;
            }

            $application->fill([
                'slug' => $slug,
                'title' => $data['title'],
                'title_en' => $data['title_en'] ?? null,
                'excerpt' => $data['excerpt'] ?? null,
                'excerpt_en' => $data['excerpt_en'] ?? null,
                'content' => $data['content'] ?? null,
                'content_en' => $data['content_en'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'sort' => $data['sort'] ?? 0,
            ]);

            if ($request->hasFile('cover')) {
                $originalCoverUrl = $application->getOriginal('cover_url');
                if ($originalCoverUrl) {
                    $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $originalCoverUrl);
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $path = $request->file('cover')->store('applications', 'public');
                $application->cover_url = '/storage/' . $path;
            }

            $application->save();

            return back()->with('success', '已更新');
        });
    }

    public function destroy(Application $application)
    {
        return DB::transaction(function () use ($application) {
            $originalCoverUrl = $application->getOriginal('cover_url');
            if ($originalCoverUrl) {
                $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $originalCoverUrl);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $application->delete();

            return redirect()->route('admin.applications.index')
                ->with('success', '應用場域已刪除');
        });
    }

    private function validateForm(Request $request, ?int $id = null): array
    {
        $slugRule = $id
            ? "unique:applications,slug,{$id}"
            : 'unique:applications,slug';

        return $request->validate([
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'excerpt_en' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'status' => ['required', 'in:draft,published'],
            'sort' => ['nullable', 'integer'],
            'cover' => ['nullable', 'image', 'max:4096'],
        ], [
            'slug.unique' => '此 Slug 已被使用，請使用其他 Slug',
            'title.required' => '請輸入應用場域名稱',
            'product_id.exists' => '指定的產品類別不存在',
            'status.required' => '請選擇狀態',
            'cover.image' => '上傳的檔案必須是圖片',
            'cover.max' => '圖片大小不能超過 4MB',
        ]);
    }

    private function getProductOptions(): array
    {
        return Product::query()
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get(['id', 'title'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                ];
            })
            ->toArray();
    }
}
