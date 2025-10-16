<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * 切换语言
     */
    public function switch(Request $request, string $locale)
    {
        // 验证语言是否支持
        if (!in_array($locale, ['zh', 'en'])) {
            $locale = 'zh'; // 默认中文
        }

        // 设置语言到 session
        Session::put('locale', $locale);
        
        // 设置应用语言
        App::setLocale($locale);

        // 返回上一页，如果没有上一页则返回首页
        $referer = $request->headers->get('referer');
        if ($referer) {
            return redirect($referer);
        }
        
        return redirect('/');
    }
}

