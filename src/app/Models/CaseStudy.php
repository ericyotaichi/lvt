<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $table = 'cases';
    protected $fillable = ['product_id', 'slug', 'title', 'title_en', 'excerpt', 'excerpt_en', 'content', 'content_en', 'cover_url', 'customer', 'customer_en', 'status', 'sort'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 多对多关系：案例说明 <-> 应用场域
    public function applications()
    {
        return $this->belongsToMany(Application::class, 'application_case', 'case_id', 'application_id')
                    ->withTimestamps();
    }

    // 通过应用场域获取关联的产品科技（间接关系）
    // 使用访问器或通过 applications 关系来获取
    public function getProductsAttribute()
    {
        return Product::whereHas('applications', function ($query) {
            $query->whereHas('cases', function ($q) {
                $q->where('cases.id', $this->id);
            });
        })->get();
    }
}
