<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['slug','title','title_en','summary','summary_en','description','description_en','content','cover_url','sort','status'];

    // 目前一頁式建立各一筆（hasOne）
    public function application() { return $this->hasOne(Application::class); }
    public function case()        { return $this->hasOne(CaseStudy::class, 'product_id'); }
    public function scopePublished($q){ return $q->where('status','published'); }
    public function scopeLatestFirst($q){ return $q->latest('id'); } // 或 created_at
    // 若未來要一對多，可加：
    // public function applications() { return $this->hasMany(Application::class); }
    // public function cases()        { return $this->hasMany(CaseStudy::class, 'product_id'); }

    // Product.php
public function applicationsDirect()
{
    return $this->hasMany(\App\Models\Application::class, 'product_id');
}

public function casesDirect()
{
    return $this->hasMany(\App\Models\CaseStudy::class, 'product_id'); // 若模型名是 Case，請用 App\Models\Case
}

public function applications() // 多對多（若無 pivot 也不會壞）
{
    return $this->belongsToMany(\App\Models\Application::class, 'application_product', 'product_id', 'application_id')
                ->withTimestamps();
}

// 注意：Product 和 CaseStudy 之间没有直接的多对多关系
// 它们通过 Application 连接：Product <-> Application <-> CaseStudy
// 如果需要获取与产品相关的案例，可以通过 applications 关系间接获取
}
