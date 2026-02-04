<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $setting = ContactSetting::query()->orderByDesc('id')->first();

        return Inertia::render('Admin/Contact/Index', [
            'setting' => $setting ? [
                'id' => $setting->id,
                'hero_image_url' => $setting->hero_image_url,
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'hero_image_url' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'image', 'max:8192'],
        ], [
            'hero_image.image' => '上傳的檔案必須是圖片',
            'hero_image.max' => '圖片大小不能超過 8MB',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $setting = ContactSetting::query()->orderByDesc('id')->first();

            if ($request->hasFile('hero_image')) {
                if ($setting && $setting->hero_image_url) {
                    $oldPath = str_replace(['/storage/', 'http://localhost/storage/', 'http://localhost:8080/storage/'], '', $setting->hero_image_url);
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $path = $request->file('hero_image')->store('contact', 'public');
                $data['hero_image_url'] = '/storage/' . $path;
            } elseif (isset($data['hero_image_url'])) {
                $data['hero_image_url'] = $data['hero_image_url'] ?: null;
            }

            unset($data['hero_image']);

            if ($setting) {
                $setting->update($data);
            } else {
                $setting = ContactSetting::create($data);
            }

            return redirect()->route('admin.contact.index')
                ->with('success', '聯絡我們設定已更新');
        });
    }
}
