<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name'=>'產品諮詢','slug'=>'product','sort'=>1],
            ['name'=>'技術合作','slug'=>'tech','sort'=>2],
            ['name'=>'其他問題','slug'=>'other','sort'=>99],
        ];
        foreach ($data as $d) { Topic::updateOrCreate(['slug'=>$d['slug']], $d); }
    }
}
