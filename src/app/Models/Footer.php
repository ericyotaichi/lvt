<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $fillable = ['content_zh', 'content_en'];
    
    /**
     * 取得當前語言的頁尾內容
     */
    public static function getContent($locale = null)
    {
        try {
            // 检查数据库连接是否可用
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            
            // 检查表是否存在
            if (!\Illuminate\Support\Facades\Schema::hasTable('footers')) {
                return '© ' . date('Y') . ' YourCompany. All rights reserved.';
            }
            
            $locale = $locale ?? app()->getLocale();
            // 使用 fresh() 确保获取最新数据，不使用缓存
            $footer = self::withoutGlobalScopes()->first();
            
            if (!$footer) {
                return '© ' . date('Y') . ' YourCompany. All rights reserved.';
            }
            
            // 重新加载模型以确保获取最新数据
            $footer->refresh();
            
            if ($locale === 'en' && !empty($footer->content_en)) {
                return str_replace('{{year}}', date('Y'), $footer->content_en);
            }
            
            return str_replace('{{year}}', date('Y'), $footer->content_zh ?? $footer->content_en ?? '© ' . date('Y') . ' YourCompany. All rights reserved.');
        } catch (\Throwable $e) {
            // 如果数据库连接失败或任何其他错误，返回默认内容
            return '© ' . date('Y') . ' YourCompany. All rights reserved.';
        }
    }
}
