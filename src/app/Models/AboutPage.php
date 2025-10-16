<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AboutPage extends Model
{
    protected $fillable = ['content_zh', 'content_en', 'image_url'];
    
    /**
     * 取得當前語言的關於我們內容
     */
    public static function getContent($locale = null)
    {
        try {
            // 检查数据库连接是否可用
            DB::connection()->getPdo();
            
            // 检查表是否存在
            if (!Schema::hasTable('about_pages')) {
                return null;
            }
            
            $locale = $locale ?? app()->getLocale();
            $about = self::first();
            
            if (!$about) {
                return null;
            }
            
            // 重新加载模型以确保获取最新数据
            $about->refresh();
            
            return [
                'content' => $locale === 'en' && !empty($about->content_en) 
                    ? $about->content_en 
                    : ($about->content_zh ?? $about->content_en ?? ''),
                'image_url' => $about->image_url,
            ];
        } catch (\Throwable $e) {
            // 如果数据库连接失败或任何其他错误，返回 null
            return null;
        }
    }
}
