<?php
// app/Http/Controllers/LeadController.php
namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function create()
    {
        try {
            $locale = app()->getLocale();
            
            // 從資料庫取得已發布的產品作為方案選項
            $plans = Product::where('status', 'published')
                ->orderBy('sort')
                ->orderBy('id')
                ->get()
                ->map(function ($product) use ($locale) {
                    $title = ($locale === 'en' && !empty($product->title_en)) 
                        ? $product->title_en 
                        : $product->title;
                    
                    return [
                        'value' => $product->slug,
                        'label' => $title,
                    ];
                })
                ->toArray();

            return Inertia::render('Leads/Create', ['plans' => $plans]);
        } catch (\Throwable $e) {
            return Inertia::render('Leads/Create', ['plans' => []]);
        }
    }

    public function store(Request $request)
    {
        try {
            // 取得所有已發布產品的 slug 列表用於驗證
            $validProductSlugs = Product::where('status', 'published')
                ->pluck('slug')
                ->toArray();

            $data = $request->validate([
                'plan'  => [
                    'required', 
                    'string', 
                    function ($attribute, $value, $fail) use ($validProductSlugs) {
                        if (!in_array($value, $validProductSlugs)) {
                            $fail('方案不在可選清單');
                        }
                    }
                ],
                'name'  => ['required','string','max:100'],
                'phone' => ['nullable','string','max:30','regex:/^[0-9+\-()\s]{7,20}$/'],
                'email' => ['required','string','email','max:255'],
                'notes' => ['nullable','string','max:1000'],
            ],[
                'plan.required' => '請選擇方案',
                'name.required' => '請輸入姓名',
                'phone.regex'   => '電話格式不正確',
                'email.email'   => 'Email 格式不正確',
            ]);

            Lead::create($data);

            return redirect()->route('leads.create')->with('success', '已收到您的需求，我們會盡快聯繫您。');
        } catch (\Throwable $e) {
            return redirect()->route('leads.create')
                ->withErrors(['error' => '提交失敗，請稍後再試。']);
        }
    }
}
