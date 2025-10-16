<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FooterController extends Controller
{
    public function index()
    {
        $footer = Footer::first();
        
        return Inertia::render('Admin/Footer/Index', [
            'footer' => $footer ? [
                'id' => $footer->id,
                'content_zh' => $footer->content_zh,
                'content_en' => $footer->content_en,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'content_zh' => ['required', 'string', 'max:1000'],
            'content_en' => ['required', 'string', 'max:1000'],
        ], [
            'content_zh.required' => '請輸入中文頁尾內容',
            'content_en.required' => '請輸入英文頁尾內容',
        ]);

        $footer = Footer::first();
        
        if ($footer) {
            $footer->update($data);
        } else {
            $footer = Footer::create($data);
        }

        // 清除缓存，确保下次请求时获取最新数据
        \Illuminate\Support\Facades\Cache::flush();
        
        return redirect()->route('admin.footer.index')
            ->with('success', '頁尾內容已更新');
    }
}
