<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TechPage extends Model
{
    protected $fillable = ['title_zh', 'title_en', 'content_zh', 'content_en'];
    
    /**
     * 取得當前語言的核心技術內容
     */
    public static function getContent($locale = null)
    {
        try {
            // 检查数据库连接是否可用
            DB::connection()->getPdo();
            
            // 检查表是否存在
            if (!Schema::hasTable('tech_pages')) {
                return null;
            }
            
            $locale = $locale ?? app()->getLocale();
            $tech = self::first();
            
            if (!$tech) {
                return null;
            }
            
            // 重新加载模型以确保获取最新数据
            $tech->refresh();
            
            $title = $locale === 'en' && trim($tech->title_en ?? '') !== ''
                ? trim($tech->title_en)
                : trim($tech->title_zh ?? $tech->title_en ?? '');
            
            $content = $locale === 'en' && trim($tech->content_en ?? '') !== ''
                ? $tech->content_en
                : ($tech->content_zh ?? $tech->content_en ?? '');
            
            return [
                'title' => $title,
                'content' => $content,
            ];
        } catch (\Throwable $e) {
            // 如果数据库连接失败或任何其他错误，返回 null
            return null;
        }
    }
}
