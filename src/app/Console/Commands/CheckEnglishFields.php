<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckEnglishFields extends Command
{
    protected $signature = 'check:english-fields';
    protected $description = 'Check if English fields exist in database';

    public function handle()
    {
        $this->info('Checking English fields...');
        
        $tables = [
            'products' => ['title_en', 'summary_en', 'description_en'],
            'applications' => ['title_en', 'excerpt_en', 'content_en'],
            'cases' => ['title_en', 'excerpt_en', 'content_en', 'customer_en'],
        ];
        
        $allExist = true;
        
        foreach ($tables as $table => $fields) {
            $this->line("Checking table: {$table}");
            foreach ($fields as $field) {
                $exists = Schema::hasColumn($table, $field);
                $status = $exists ? '✓' : '✗';
                $this->line("  {$status} {$field}");
                if (!$exists) {
                    $allExist = false;
                }
            }
        }
        
        if ($allExist) {
            $this->info('All English fields exist!');
        } else {
            $this->error('Some English fields are missing. Please run: php artisan migrate');
        }
        
        return $allExist ? 0 : 1;
    }
}

