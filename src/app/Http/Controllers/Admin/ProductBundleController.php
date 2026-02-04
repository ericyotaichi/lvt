<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductBundleController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/ProductBundle/Form', [
            'mode' => 'create',
            'product' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'        => ['nullable','string','max:255','unique:products,slug'],
            'title'       => ['required','string','max:255'],
            'title_en'    => ['nullable','string','max:255'],
            'summary'     => ['nullable','string','max:500'],
            'summary_en'  => ['nullable','string','max:500'],
            'content'     => ['nullable','string'],
            'content_en'  => ['nullable','string'],
            'category'    => ['nullable','string'],
            'status'      => ['required','in:draft,published'],
            'sort'        => ['nullable','integer'],
            'cover'       => ['nullable','image','max:4096'],
        ], [
            'slug.unique' => '此 Slug 已被使用，請使用其他 Slug',
            'title.required' => '請輸入產品名稱',
            'status.required' => '請選擇狀態',
            'cover.image' => '上傳的檔案必須是圖片',
            'cover.max' => '圖片大小不能超過 4MB',
        ]);

        return DB::transaction(function () use ($data, $request) {
            // 如果没有提供 slug，自动生成
            $slug = $data['slug'] ?? null;
            if (!$slug) {
                $slug = Str::slug($data['title']);
                // 检查自动生成的 slug 是否已存在
                $counter = 1;
                $originalSlug = $slug;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            $product = new Product([
                'slug'        => $slug,
                'title'       => $data['title'],
                'title_en'    => $data['title_en'] ?? null,
                'summary'     => $data['summary'] ?? null,
                'summary_en'  => $data['summary_en'] ?? null,
                'content'     => $data['content'] ?? null,
                'content_en'  => $data['content_en'] ?? null,
                'status'      => $data['status'] ?? 'draft',
                'sort'        => $data['sort'] ?? 0,
            ]);
            $product->category = null;
            
            if ($request->hasFile('cover')) {
                $path = $request->file('cover')->store('products','public');
                // 使用相对路径，避免端口问题
                $product->cover_url = '/storage/' . $path;
            }
            
            $product->save();

            return redirect()->route('admin.products.edit', $product->id)
                ->with('success','已建立產品與服務');
        });
    }

    public function edit(Product $product)
    {
        // 重新加载以确保获取最新数据
        $product->refresh();
        
        // 手动构建数组，确保 cover_url 使用访问器
        $productData = [
            'id' => $product->id,
            'slug' => $product->slug,
            'title' => $product->title,
            'title_en' => $product->title_en,
            'summary' => $product->summary,
            'summary_en' => $product->summary_en,
            'content' => $product->content,
            'content_en' => $product->content_en,
            'category' => $product->category,
            'status' => $product->status,
            'sort' => $product->sort,
            'cover_url' => $product->cover_url, // 使用访问器确保是相对路径
        ];
        
        // 调试：记录传递给前端的数据
        \Log::info('Product edit data', [
            'id' => $product->id,
            'cover_url_raw' => $product->getOriginal('cover_url'),
            'cover_url_accessor' => $product->cover_url,
            'cover_url_in_array' => $productData['cover_url'] ?? null
        ]);
        
        return Inertia::render('Admin/ProductBundle/Form', [
            'mode'     => 'edit',
            'product'  => $productData,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'slug'        => ['nullable','string','max:255',"unique:products,slug,{$product->id}"],
            'title'       => ['required','string','max:255'],
            'title_en'    => ['nullable','string','max:255'],
            'summary'     => ['nullable','string','max:500'],
            'summary_en'  => ['nullable','string','max:500'],
            'content'     => ['nullable','string'],
            'content_en'  => ['nullable','string'],
            'category'    => ['nullable','string'],
            'status'      => ['required','in:draft,published'],
            'sort'        => ['nullable','integer'],
            'cover'       => ['nullable','image','max:4096'],
        ], [
            'slug.unique' => '此 Slug 已被使用，請使用其他 Slug',
            'title.required' => '請輸入產品名稱',
            'status.required' => '請選擇狀態',
            'cover.image' => '上傳的檔案必須是圖片',
            'cover.max' => '圖片大小不能超過 4MB',
        ]);

        return DB::transaction(function () use ($data, $request, $product) {
            // 处理 slug：如果提供了新值，使用新值；如果为空且原产品没有 slug，自动生成
            $slug = $data['slug'] ?? null;
            if (!$slug && !$product->slug) {
                $slug = Str::slug($data['title']);
                // 检查自动生成的 slug 是否已存在（排除当前产品）
                $counter = 1;
                $originalSlug = $slug;
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            } elseif (!$slug) {
                // 如果没有提供新值，保持原有 slug
                $slug = $product->slug;
            }
            
            $product->fill([
                'slug'        => $slug,
                'title'       => $data['title'],
                'title_en'    => $data['title_en'] ?? null,
                'summary'     => $data['summary'] ?? null,
                'summary_en'  => $data['summary_en'] ?? null,
                'content'     => $data['content'] ?? null,
                'content_en'  => $data['content_en'] ?? null,
                'status'      => $data['status'] ?? 'draft',
                'sort'        => $data['sort'] ?? 0,
            ]);
            $product->category = null;
            
            if ($request->hasFile('cover')) {
                try {
                    // 删除旧图片
                    if ($product->cover_url) {
                        // 获取原始值（不使用访问器）
                        $originalCoverUrl = $product->getOriginal('cover_url');
                        if ($originalCoverUrl) {
                            // 处理各种可能的 URL 格式，获取实际的文件路径
                            $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $originalCoverUrl);
                            // 也处理 url() 生成的路径
                            try {
                                $storageUrl = url('storage/');
                                if ($storageUrl && strpos($oldPath, $storageUrl) === 0) {
                                    $oldPath = str_replace($storageUrl, '', $oldPath);
                                }
                            } catch (\Exception $e) {
                                // 忽略错误
                            }
                            
                            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                                Storage::disk('public')->delete($oldPath);
                            }
                        }
                    }
                    
                    // 保存新图片
                    $path = $request->file('cover')->store('products','public');
                    // 使用相对路径，避免端口问题
                    // store() 返回的路径格式是 'products/filename.jpg'
                    $coverUrl = '/storage/' . $path;
                    
                    // 使用 setAttribute 直接设置，确保保存的是相对路径
                    $product->setAttribute('cover_url', $coverUrl);
                    
                    // 验证文件确实存在
                    if (!Storage::disk('public')->exists($path)) {
                        \Log::error('Cover image file not found after upload', [
                            'path' => $path,
                            'full_path' => Storage::disk('public')->path($path)
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error handling cover image upload', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }
            
            $product->save();
            
            // 调试：确认保存后的值
            \Log::info('Product saved', [
                'id' => $product->id,
                'cover_url_in_db' => $product->getOriginal('cover_url'),
                'cover_url_accessor' => $product->cover_url
            ]);

            return back()->with('success','已更新');
        });
    }

    public function destroy(Product $product)
    {
        return DB::transaction(function () use ($product) {
            // 删除关联的图片文件
            $originalCoverUrl = $product->getOriginal('cover_url');
            if ($originalCoverUrl) {
                // 处理各种可能的 URL 格式，获取实际的文件路径
                $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $originalCoverUrl);
                // 也处理 url() 生成的路径
                try {
                    $storageUrl = url('storage/');
                    if ($storageUrl && strpos($oldPath, $storageUrl) === 0) {
                        $oldPath = str_replace($storageUrl, '', $oldPath);
                    }
                } catch (\Exception $e) {
                    // 忽略错误
                }
                
                if ($oldPath && $oldPath !== $originalCoverUrl && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // 最后删除 Product
            $product->delete();

            return redirect()->route('admin.articles.index')
                ->with('success', '產品已刪除');
        });
    }
}
