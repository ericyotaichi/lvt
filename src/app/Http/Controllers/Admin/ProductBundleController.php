<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Application;
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
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Product
            'product.slug'        => ['nullable','string','max:255','unique:products,slug'],
            'product.title'       => ['required','string','max:255'],
            'product.title_en'    => ['nullable','string','max:255'],
            'product.summary'     => ['nullable','string','max:255'],
            'product.summary_en'  => ['nullable','string','max:255'],
            'product.description' => ['nullable','string'],
            'product.description_en' => ['nullable','string'],
            'product.status'      => ['required','in:draft,published'],
            'product.sort'        => ['nullable','integer'],
            'product.cover'       => ['nullable','image','max:4096'],

            // Application
            'application.slug'    => ['nullable','string','max:255','unique:applications,slug'],
            'application.title'   => ['nullable','string','max:255'],
            'application.title_en' => ['nullable','string','max:255'],
            'application.excerpt' => ['nullable','string','max:255'],
            'application.excerpt_en' => ['nullable','string','max:255'],
            'application.content' => ['nullable','string'],
            'application.content_en' => ['nullable','string'],
            'application.status'  => ['nullable','in:draft,published'],
            'application.sort'    => ['nullable','integer'],
            'application.cover'   => ['nullable','image','max:4096'],

            // Case
            'case.slug'           => ['nullable','string','max:255','unique:cases,slug'],
            'case.title'          => ['nullable','string','max:255'],
            'case.title_en'       => ['nullable','string','max:255'],
            'case.excerpt'        => ['nullable','string','max:255'],
            'case.excerpt_en'     => ['nullable','string','max:255'],
            'case.content'        => ['nullable','string'],
            'case.content_en'     => ['nullable','string'],
            'case.customer'       => ['nullable','string','max:255'],
            'case.customer_en'    => ['nullable','string','max:255'],
            'case.status'         => ['nullable','in:draft,published'],
            'case.sort'           => ['nullable','integer'],
            'case.cover'          => ['nullable','image','max:4096'],

            // 同步發布（可選）
            'sync_publish'        => ['nullable','boolean'],
        ]);

        // 預設：應用/案例承接產品資料（可被傳入值覆寫）
        $p  = $data['product'];
        $ap = $data['application'] ?? [];
        $ca = $data['case'] ?? [];

        return DB::transaction(function () use ($p, $ap, $ca, $request) {
            // Product
            $product = new Product([
                'slug'        => $p['slug'] ?? null,
                'title'       => $p['title'],
                'title_en'    => $p['title_en'] ?? null,
                'summary'     => $p['summary'] ?? null,
                'summary_en'  => $p['summary_en'] ?? null,
                'description' => $p['description'] ?? null,
                'description_en' => $p['description_en'] ?? null,
                'status'      => $p['status'] ?? 'draft',
                'sort'        => $p['sort'] ?? 0,
            ]);

            if (!$product->slug) $product->slug = Str::slug($product->title);
            if ($request->hasFile('product.cover')) {
                $path = $request->file('product.cover')->store('products','public');
                $product->cover_url = Storage::disk('public')->url($path);
            }
            $product->save();

            // Application（承接產品，允許覆寫）
            $application = new Application([
                'product_id'  => $product->id,
                'slug'        => $ap['slug']   ?? Str::slug($product->slug.'-application'),
                'title'       => $ap['title']  ?? $product->title,
                'title_en'    => $ap['title_en'] ?? $product->title_en,
                'excerpt'     => $ap['excerpt']?? $product->summary,
                'excerpt_en'  => $ap['excerpt_en'] ?? $product->summary_en,
                'content'     => $ap['content']?? null,
                'content_en'  => $ap['content_en'] ?? null,
                'status'      => $ap['status'] ?? $product->status,
                'sort'        => $ap['sort']   ?? $product->sort,
            ]);
            if ($request->hasFile('application.cover')) {
                $path = $request->file('application.cover')->store('applications','public');
                $application->cover_url = Storage::disk('public')->url($path);
            }
            $application->save();

            // Case（承接產品，允許覆寫）
            $case = new CaseStudy([
                'product_id'  => $product->id,
                'slug'        => $ca['slug']     ?? Str::slug($product->slug.'-case'),
                'title'       => $ca['title']    ?? $product->title,
                'title_en'    => $ca['title_en'] ?? $product->title_en,
                'excerpt'     => $ca['excerpt']  ?? $product->summary,
                'excerpt_en'  => $ca['excerpt_en'] ?? $product->summary_en,
                'content'     => $ca['content']  ?? null,
                'content_en' => $ca['content_en'] ?? null,
                'customer'    => $ca['customer'] ?? null,
                'customer_en' => $ca['customer_en'] ?? null,
                'status'      => $ca['status']   ?? $product->status,
                'sort'        => $ca['sort']     ?? $product->sort,
            ]);
            if ($request->hasFile('case.cover')) {
                $path = $request->file('case.cover')->store('cases','public');
                $case->cover_url = Storage::disk('public')->url($path);
            }
            $case->save();

            return redirect()->route('admin.products.edit', $product->id)
                ->with('success','已建立產品＋應用＋案例');
        });
    }

    public function edit(Product $product)
    {
        $product->load(['application','case']);
        return Inertia::render('Admin/ProductBundle/Form', [
            'mode'     => 'edit',
            'product'  => $product,
            'application' => $product->application,
            'case'        => $product->case,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'product.slug'        => ['nullable','string','max:255',"unique:products,slug,{$product->id}"],
            'product.title'       => ['required','string','max:255'],
            'product.title_en'    => ['nullable','string','max:255'],
            'product.summary'     => ['nullable','string','max:255'],
            'product.summary_en'  => ['nullable','string','max:255'],
            'product.description' => ['nullable','string'],
            'product.description_en' => ['nullable','string'],
            'product.status'      => ['required','in:draft,published'],
            'product.sort'        => ['nullable','integer'],
            'product.cover'       => ['nullable','image','max:4096'],

            'application.slug'    => ['nullable','string','max:255', 'unique:applications,slug,' . optional($product->application)->id],
            'application.title'   => ['nullable','string','max:255'],
            'application.title_en' => ['nullable','string','max:255'],
            'application.excerpt' => ['nullable','string','max:255'],
            'application.excerpt_en' => ['nullable','string','max:255'],
            'application.content' => ['nullable','string'],
            'application.content_en' => ['nullable','string'],
            'application.status'  => ['nullable','in:draft,published'],
            'application.sort'    => ['nullable','integer'],
            'application.cover'   => ['nullable','image','max:4096'],

            'case.slug'           => ['nullable','string','max:255', 'unique:cases,slug,' . optional($product->case)->id],
            'case.title'          => ['nullable','string','max:255'],
            'case.title_en'       => ['nullable','string','max:255'],
            'case.excerpt'        => ['nullable','string','max:255'],
            'case.excerpt_en'     => ['nullable','string','max:255'],
            'case.content'        => ['nullable','string'],
            'case.content_en'     => ['nullable','string'],
            'case.customer'       => ['nullable','string','max:255'],
            'case.customer_en'    => ['nullable','string','max:255'],
            'case.status'         => ['nullable','in:draft,published'],
            'case.sort'           => ['nullable','integer'],
            'case.cover'          => ['nullable','image','max:4096'],
        ]);

        return DB::transaction(function () use ($data, $request, $product) {
            // Product
            $p = $data['product'];
            $product->fill([
                'slug'        => $p['slug'] ?? ($product->slug ?: Str::slug($p['title'])),
                'title'       => $p['title'],
                'title_en'    => $p['title_en'] ?? null,
                'summary'     => $p['summary'] ?? null,
                'summary_en'  => $p['summary_en'] ?? null,
                'description' => $p['description'] ?? null,
                'description_en' => $p['description_en'] ?? null,
                'status'      => $p['status'] ?? 'draft',
                'sort'        => $p['sort'] ?? 0,
            ]);
            if ($request->hasFile('product.cover')) {
                $path = $request->file('product.cover')->store('products','public');
                $product->cover_url = Storage::disk('public')->url($path);
            }
            $product->save();

            // Application
            $apModel = $product->application ?: new Application(['product_id'=>$product->id]);
            $ap = $data['application'] ?? [];
            $apModel->fill([
                'slug'        => $ap['slug']   ?? ($apModel->slug ?: Str::slug($product->slug.'-application')),
                'title'       => $ap['title']  ?? $product->title,
                'title_en'    => $ap['title_en'] ?? $product->title_en,
                'excerpt'     => $ap['excerpt']?? $product->summary,
                'excerpt_en'  => $ap['excerpt_en'] ?? $product->summary_en,
                'content'     => $ap['content']?? null,
                'content_en'  => $ap['content_en'] ?? null,
                'status'      => $ap['status'] ?? $product->status,
                'sort'        => $ap['sort']   ?? $product->sort,
            ]);
            if ($request->hasFile('application.cover')) {
                $path = $request->file('application.cover')->store('applications','public');
                $apModel->cover_url = Storage::disk('public')->url($path);
            }
            $apModel->save();

            // Case
            $caModel = $product->case ?: new CaseStudy(['product_id'=>$product->id]);
            $ca = $data['case'] ?? [];
            $caModel->fill([
                'slug'        => $ca['slug']     ?? ($caModel->slug ?: Str::slug($product->slug.'-case')),
                'title'       => $ca['title']    ?? $product->title,
                'title_en'    => $ca['title_en'] ?? $product->title_en,
                'excerpt'     => $ca['excerpt']  ?? $product->summary,
                'excerpt_en'  => $ca['excerpt_en'] ?? $product->summary_en,
                'content'     => $ca['content']  ?? null,
                'content_en' => $ca['content_en'] ?? null,
                'customer'    => $ca['customer'] ?? null,
                'customer_en' => $ca['customer_en'] ?? null,
                'status'      => $ca['status']   ?? $product->status,
                'sort'        => $ca['sort']     ?? $product->sort,
            ]);
            if ($request->hasFile('case.cover')) {
                $path = $request->file('case.cover')->store('cases','public');
                $caModel->cover_url = Storage::disk('public')->url($path);
            }
            $caModel->save();

            return back()->with('success','已更新');
        });
    }

    public function destroy(Product $product)
    {
        return DB::transaction(function () use ($product) {
            // 删除关联的图片文件
            if ($product->cover_url) {
                $oldPath = str_replace(url('storage/'), '', $product->cover_url);
                if ($oldPath && $oldPath !== $product->cover_url && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // 删除关联的 Application
            if ($product->application) {
                $application = $product->application;
                if ($application->cover_url) {
                    $oldPath = str_replace(url('storage/'), '', $application->cover_url);
                    if ($oldPath && $oldPath !== $application->cover_url && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $application->delete();
            }

            // 删除关联的 Case
            if ($product->case) {
                $case = $product->case;
                if ($case->cover_url) {
                    $oldPath = str_replace(url('storage/'), '', $case->cover_url);
                    if ($oldPath && $oldPath !== $case->cover_url && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                $case->delete();
            }

            // 最后删除 Product
            $product->delete();

            return redirect()->route('admin.articles.index')
                ->with('success', '文章已刪除');
        });
    }
}
