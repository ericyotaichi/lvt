<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        $about = AboutPage::first();
        
        return Inertia::render('Admin/About/Index', [
            'about' => $about ? [
                'id' => $about->id,
                'content_zh' => $about->content_zh,
                'content_en' => $about->content_en,
                'image_url' => $about->image_url,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        \Log::info('About update request', [
            'hasFile' => $request->hasFile('image'),
            'allFiles' => array_keys($request->allFiles()),
            'content_zh' => $request->has('content_zh'),
            'content_en' => $request->has('content_en'),
            'image_url' => $request->input('image_url'),
        ]);

        $data = $request->validate([
            'content_zh' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'], // 10MB max
        ], [
            'content_zh.required' => '請輸入中文公司介紹內容',
            'content_en.required' => '請輸入英文公司介紹內容',
            'image.image' => '上傳的檔案必須是圖片',
            'image.mimes' => '圖片格式必須是 jpeg, jpg, png, gif 或 webp',
            'image.max' => '圖片大小不能超過 10MB',
        ]);

        $about = AboutPage::first();
        
        // 处理图片上传
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                \Log::info('About image upload', [
                    'originalName' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mimeType' => $file->getMimeType(),
                ]);
                
                // 如果已有图片，先删除旧图片
                if ($about && $about->image_url) {
                    $oldPath = str_replace(url('storage/'), '', $about->image_url);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                
                $path = $file->store('about', 'public');
                $data['image_url'] = url('storage/' . $path);
                \Log::info('About image uploaded', ['path' => $path, 'url' => $data['image_url']]);
            } catch (\Exception $e) {
                \Log::error('About image upload failed', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withErrors(['image' => '圖片上傳失敗：' . $e->getMessage()])
                    ->withInput();
            }
        } elseif (isset($data['image_url']) && empty($data['image_url'])) {
            // 如果清空了 image_url，保留原有图片
            if ($about && $about->image_url) {
                $data['image_url'] = $about->image_url;
            }
        }
        
        // 移除 image 字段（如果存在），因为我们已经处理了图片上传
        unset($data['image']);
        
        if ($about) {
            $about->update($data);
            \Log::info('About page updated', ['id' => $about->id]);
        } else {
            $about = AboutPage::create($data);
            \Log::info('About page created', ['id' => $about->id]);
        }

        // 清除缓存，确保下次请求时获取最新数据
        \Illuminate\Support\Facades\Cache::flush();
        
        return redirect()->route('admin.about.index')
            ->with('success', '關於我們內容已更新');
    }
}
