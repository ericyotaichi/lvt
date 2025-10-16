<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'slug' => 'solution-a',
                'title' => '方案 A',
                'summary' => '標準化解決方案',
                'description' => '適合中小企業快速導入。',
                'cover_url' => null,
                'sort' => 10,
                'status' => 'published',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'slug' => 'solution-b',
                'title' => '方案 B',
                'summary' => '進階客製方案',
                'description' => '可擴充與整合，支援大型場域。',
                'cover_url' => null,
                'sort' => 20,
                'status' => 'published',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
