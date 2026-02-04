<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['slug','title','title_en','summary','summary_en','description','description_en','content','content_en','cover_url','sort','status'];

    /**
     * 确保 cover_url 始终返回相对路径（仅在读取时）
     * 注意：这个访问器只在读取属性时生效，不会影响保存
     */
    public function getCoverUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // 如果已经是相对路径，直接返回
        if (strpos($value, '/storage/') === 0) {
            return $value;
        }
        
        // 如果是绝对路径，转换为相对路径
        // 先处理各种可能的绝对路径格式
        $patterns = [
            '~^https?://[^/]+/storage/~' => '/storage/',
            '~^https?://localhost(:[0-9]+)?/storage/~' => '/storage/',
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            $value = preg_replace($pattern, $replacement, $value);
        }
        
        // 也处理 url() 函数生成的路径（使用 try-catch 避免在某些情况下出错）
        try {
            $storageUrl = url('storage/');
            if ($storageUrl && strpos($value, $storageUrl) === 0) {
                $value = '/storage/' . substr($value, strlen($storageUrl));
            }
        } catch (\Exception $e) {
            // 如果 url() 函数出错，忽略
        }
        
        // 确保以 /storage/ 开头
        if (strpos($value, '/storage/') !== 0) {
            // 如果只是文件名，添加路径
            if (strpos($value, 'products/') === false && strpos($value, '/') === false) {
                $value = '/storage/products/' . $value;
            } else if (strpos($value, 'products/') !== false) {
                $value = '/storage/' . $value;
            }
        }
        
        return $value;
    }
    
    /**
     * 保存时确保 cover_url 格式正确（相对路径）
     */
    public function setCoverUrlAttribute($value)
    {
        if (!$value) {
            $this->attributes['cover_url'] = null;
            return;
        }
        
        // 确保保存的是相对路径格式
        if (strpos($value, '/storage/') === 0) {
            // 已经是相对路径，直接保存
            $this->attributes['cover_url'] = $value;
        } else {
            // 转换为相对路径
            $value = str_replace([
                url('storage/'),
                'http://localhost/storage/',
                'http://localhost:8080/storage/',
                'https://localhost/storage/',
                'https://localhost:8080/storage/',
            ], '/storage/', $value);
            
            // 确保以 /storage/ 开头
            if (strpos($value, '/storage/') !== 0) {
                if (strpos($value, 'products/') === false) {
                    $value = '/storage/products/' . $value;
                } else {
                    $value = '/storage/' . $value;
                }
            }
            
            $this->attributes['cover_url'] = $value;
        }
    }

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
