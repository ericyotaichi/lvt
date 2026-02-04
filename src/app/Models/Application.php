<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'product_id',
        'slug',
        'title',
        'title_en',
        'excerpt',
        'excerpt_en',
        'content',
        'content_en',
        'cover_url',
        'status',
        'sort'
    ];

    public function product() { return $this->belongsTo(Product::class); }

    // 多对多关系：应用场域 <-> 产品科技
    public function products()
    {
        return $this->belongsToMany(Product::class, 'application_product', 'application_id', 'product_id')
                    ->withTimestamps();
    }

    // 多对多关系：应用场域 <-> 案例说明
    public function cases()
    {
        return $this->belongsToMany(CaseStudy::class, 'application_case', 'application_id', 'case_id')
                    ->withTimestamps();
    }
}