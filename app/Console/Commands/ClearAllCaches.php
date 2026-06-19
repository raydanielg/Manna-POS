<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ClearAllCaches extends Command
{
    protected $signature = 'cache:clear-all {--force : Run without confirmation}';
    protected $description = 'Clear ALL caches (config, view, route, application, compiled, cache)';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will clear ALL caches. Continue?')) {
            return;
        }

        $this->info('Clearing ALL caches...');
        $this->newLine();

        $caches = [
            'Configuration cache...' => fn() => Artisan::call('config:clear'),
            'Application cache...'   => fn() => Artisan::call('cache:clear'),
            'View cache...'          => fn() => Artisan::call('view:clear'),
            'Route cache...'         => fn() => Artisan::call('route:clear'),
            'Compiled classes...'    => fn() => Artisan::call('clear-compiled'),
            'Event cache...'         => fn() => Artisan::call('event:clear'),
            'Optimize clear...'      => fn() => Artisan::call('optimize:clear'),
        ];

        foreach ($caches as $label => $callback) {
            $this->info("  → {$label}");
            $callback();
        }

        $this->newLine();
        $this->info('All caches cleared successfully!');
        $this->comment('Run `php artisan config:cache` if you want to re-cache config.');

        return 0;
    }
}
