<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TechController extends Controller
{
    public function index()
    {
        $tech = TechPage::first();
        
        return Inertia::render('Admin/Tech/Index', [
            'tech' => $tech ? [
                'id' => $tech->id,
                'title_zh' => $tech->title_zh,
                'title_en' => $tech->title_en,
                'content_zh' => $tech->content_zh,
                'content_en' => $tech->content_en,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title_zh' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content_zh' => ['required', 'string'],
            'content_en' => ['nullable', 'string'],
        ], [
            'title_zh.required' => '請輸入中文標題',
            'content_zh.required' => '請輸入中文內容',
        ]);

        $tech = TechPage::first();
        
        if ($tech) {
            $tech->update($data);
        } else {
            $tech = TechPage::create($data);
        }

        return redirect()->route('admin.tech.index')
            ->with('success', '核心技術內容已更新');
    }
}

