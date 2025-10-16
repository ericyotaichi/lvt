<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarouselController extends Controller
{
    public function index()
    {
        $slides = CarouselSlide::orderBy('sort')->orderBy('id')->get();
        
        return Inertia::render('Admin/Carousel/Index', [
            'slides' => $slides,
        ]);
    }

    public function store(Request $request)
    {
        // 先记录所有请求数据（验证前）
        \Log::info('Carousel store request (before validation)', [
            'hasFile' => $request->hasFile('image'),
            'allFiles' => array_keys($request->allFiles()),
            'allInput' => array_keys($request->all()),
            'requestMethod' => $request->method(),
            'contentType' => $request->header('Content-Type'),
            'requestSize' => $request->header('Content-Length'),
        ]);

        // 验证规则：image 字段应该是文件，但如果验证失败也不要阻止其他数据
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'], // 10MB max
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:100'],
            'link_text_en' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ], [
            'image.image' => '上傳的檔案必須是圖片',
            'image.mimes' => '圖片格式必須是 jpeg, jpg, png, gif 或 webp',
            'image.max' => '圖片大小不能超過 10MB',
        ]);

        // 处理图片上传
        \Log::info('Carousel store request (after validation)', [
            'hasFile' => $request->hasFile('image'),
            'allFiles' => array_keys($request->allFiles()),
            'image_url' => $data['image_url'] ?? null,
            'validatedData' => array_keys($data),
            'requestAll' => $request->all(),
        ]);
        
        // 检查文件是否真的存在
        if ($request->hasFile('image')) {
            \Log::info('File found!', [
                'fileName' => $request->file('image')->getClientOriginalName(),
                'fileSize' => $request->file('image')->getSize(),
                'mimeType' => $request->file('image')->getMimeType(),
            ]);
        } else {
            \Log::warning('No file found in request!', [
                'allFiles' => $request->allFiles(),
                'hasFile_image' => $request->hasFile('image'),
            ]);
        }
        
        // 尝试多种方式获取文件
        $imageFile = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
        } elseif ($request->has('image')) {
            // 尝试从请求中直接获取
            $imageFile = $request->input('image');
        }
        
        \Log::info('File check result', [
            'hasFile' => $request->hasFile('image'),
            'imageFile' => $imageFile ? 'exists' : 'null',
            'allFiles' => array_keys($request->allFiles()),
        ]);
        
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                \Log::info('Attempting to store file', [
                    'originalName' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mimeType' => $file->getMimeType(),
                ]);
                
                $path = $file->store('carousel', 'public');
                // 使用 config 中的 url 配置生成完整 URL
                $data['image_url'] = url('storage/' . $path);
                \Log::info('Image uploaded successfully', ['path' => $path, 'url' => $data['image_url']]);
            } catch (\Exception $e) {
                \Log::error('Image upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()
                    ->withErrors(['image' => '圖片上傳失敗：' . $e->getMessage()])
                    ->withInput();
            }
        } elseif (empty($data['image_url'])) {
            // 如果没有上传图片也没有提供 URL，返回错误
            \Log::warning('No image provided', [
                'hasFile' => $request->hasFile('image'),
                'image_url' => $data['image_url'] ?? null,
            ]);
            return redirect()->back()
                ->withErrors(['image' => '請上傳圖片或輸入圖片URL'])
                ->withInput();
        }

        CarouselSlide::create($data);

        return redirect()->route('admin.carousel.index')
            ->with('success', '輪播項目已新增');
    }

    public function update(Request $request, CarouselSlide $carousel)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'description_en' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:5120'], // 5MB max
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:100'],
            'link_text_en' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);

        // 处理图片上传
        if ($request->hasFile('image')) {
            // 删除旧图片（如果存在）
            if ($carousel->image_url) {
                $oldPath = str_replace(url('storage/'), '', $carousel->image_url);
                if ($oldPath && $oldPath !== $carousel->image_url) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('carousel', 'public');
            // 使用 config 中的 url 配置生成完整 URL
            $data['image_url'] = url('storage/' . $path);
        }

        $carousel->update($data);

        return redirect()->route('admin.carousel.index')
            ->with('success', '輪播項目已更新');
    }

    public function destroy(CarouselSlide $carousel)
    {
        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', '輪播項目已刪除');
    }
}
