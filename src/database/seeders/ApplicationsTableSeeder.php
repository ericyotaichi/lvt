<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('applications')->insert([
            [
                'slug' => 'smart-factory',
                'title' => '智慧工廠',
                'excerpt' => '能耗監控與最佳化',
                'content' => '以感測資料與 AI 達成降本增效。',
                'cover_url' => null,
                'status' => 'published',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'slug' => 'smart-building',
                'title' => '智慧建築',
                'excerpt' => '空調照明最佳化',
                'content' => '舒適度與節能兼顧。',
                'cover_url' => null,
                'status' => 'published',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
