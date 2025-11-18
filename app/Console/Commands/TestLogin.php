<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestLogin extends Command
{
    protected $signature = 'test:login';
    protected $description = 'Test login redirect logic';

    public function handle()
    {
        $this->info('Testing login redirect logic...');
        
        // Test referer detection
        $referers = [
            'http://localhost:8000/client/login',
            'http://localhost:8000/admin/login',
            'http://localhost:8000/some/other/page'
        ];
        
        foreach ($referers as $referer) {
            $this->info("Referer: $referer");
            if (str_contains($referer, '/client/login')) {
                $this->info("  -> Would redirect to client dashboard");
            } elseif (str_contains($referer, '/admin/login')) {
                $this->info("  -> Would redirect to admin dashboard");
            } else {
                $this->info("  -> Would use fallback logic");
            }
        }
    }
}