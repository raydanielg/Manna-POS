<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductBatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpiryReminderCommand extends Command
{
    protected $signature = 'mannapos:expiry-reminders {--days=7 : Days ahead to check}';
    protected $description = 'Check for products nearing expiry and notify users';

    public function handle()
    {
        $days = (int) $this->option('days');
        $threshold = now()->addDays($days);

        $expiring = ProductBatch::with(['product:id,name,sku', 'supplier:id,name'])
            ->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', $threshold)
            ->orderBy('expiry_date')
            ->get();

        $expired = ProductBatch::with(['product:id,name,sku', 'supplier:id,name'])
            ->where('status', 'active')
            ->where('expiry_date', '<', now())
            ->orderBy('expiry_date')
            ->get();

        $this->info("Expiring in {$days} days: " . $expiring->count());
        $this->info("Already expired: " . $expired->count());

        if ($expiring->isEmpty() && $expired->isEmpty()) {
            $this->info('No expiry alerts at this time.');
            return 0;
        }

        // Update expired batches to 'expired' status
        ProductBatch::where('status', 'active')
            ->where('expiry_date', '<', now())
            ->update(['status' => 'expired']);

        $this->info('Expired batch statuses updated.');
        return 0;
    }
}
