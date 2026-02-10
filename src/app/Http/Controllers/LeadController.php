<?php
// app/Http/Controllers/LeadController.php
namespace App\Http\Controllers;

use App\Mail\LeadSubmitted;
use App\Models\Lead;
use App\Models\Product;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

            $contactSetting = ContactSetting::query()->orderByDesc('id')->first();

            return Inertia::render('Leads/Create', [
                'plans' => $plans,
                'contactSetting' => $contactSetting ? [
                    'hero_image_url' => $contactSetting->hero_image_url,
                ] : null,
            ]);
        } catch (\Throwable $e) {
            return Inertia::render('Leads/Create', [
                'plans' => [],
                'contactSetting' => null,
            ]);
        }
    }

    public function store(Request $request)
    {
        Log::info('Lead store request received', ['plan' => $request->input('plan'), 'name' => $request->input('name')]);

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
                        if ($value === 'other') {
                            return;
                        }
                        if (!in_array($value, $validProductSlugs)) {
                            $fail('方案不在可選清單');
                        }
                    }
                ],
                'name'  => ['required','string','max:100'],
                'phone' => ['nullable','string','max:30','regex:/^[0-9+\-()\s]{7,20}$/'],
                'email' => ['required','string','email','max:255'],
                'notes' => ['nullable','string','max:1000'],
            ], [
                'plan.required' => '請選擇方案',
                'name.required' => '請輸入姓名',
                'phone.regex'   => '電話格式不正確',
                'email.email'   => 'Email 格式不正確',
            ]);

            // XSS 防護：移除 HTML 標籤
            $sanitized = [
                'plan' => $data['plan'],
                'name' => strip_tags($data['name'] ?? ''),
                'phone' => strip_tags($data['phone'] ?? ''),
                'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'notes' => strip_tags($data['notes'] ?? ''),
                'message' => strip_tags($data['notes'] ?? ''),
                'lead_token' => Str::random(40),
            ];
            $lead = Lead::create($sanitized);

            Log::info('Lead form submitted', [
                'plan' => $lead->plan,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
            ]);

            try {
                Mail::to('t0921206292@gmail.com')->send(new LeadSubmitted($lead));
                Log::info('Lead notification email dispatched', ['lead_id' => $lead->id]);
            } catch (\Throwable $mailException) {
                Log::error('Lead notification email failed', [
                    'lead_id' => $lead->id,
                    'message' => $mailException->getMessage(),
                ]);
                report($mailException);
            }

            return redirect()->route('leads.create')->with('success', __('common.lead.success'));
        } catch (\Throwable $e) {
            Log::error('Lead store failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('leads.create')
                ->withErrors(['error' => __('common.lead.submit_failed', ['message' => $e->getMessage()])]);
        }
    }
}
