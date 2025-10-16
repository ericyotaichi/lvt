<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CasesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cases')->insert([
            [
                'slug' => 'abc-corp-energy',
                'title' => 'ABC 電子廠節能優化',
                'customer' => 'ABC Corp',
                'excerpt' => '年節能 18%，ROI 8 個月',
                'content' => '導入設備監測與最佳化控制後，能耗明顯下降。',
                'cover_url' => null,
                'metrics' => json_encode([
                    ['label' => '年節能', 'value' => '18%'],
                    ['label' => 'ROI', 'value' => '8 個月'],
                ]),
                'status' => 'published',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}
