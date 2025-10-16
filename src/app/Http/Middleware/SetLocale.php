<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 从 session 获取语言，如果没有则使用默认语言（中文）
        $locale = Session::get('locale', 'zh');
        
        // 验证语言是否支持
        if (!in_array($locale, ['zh', 'en'])) {
            $locale = 'zh';
        }

        // 设置应用语言
        App::setLocale($locale);

        return $next($request);
    }
}

