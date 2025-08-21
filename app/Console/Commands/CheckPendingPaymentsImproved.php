<?php
// File: app/Console/Commands/CheckPendingPaymentsImproved.php
// Command yang dispatch individual jobs untuk setiap transaksi

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Jobs\CheckPaymentStatus;
use Illuminate\Support\Facades\Log;

class CheckPendingPaymentsImproved extends Command
{
    protected $signature = 'payment:check-pending-improved 
                            {--sync : Run synchronously instead of using queue}
                            {--limit=50 : Limit number of transactions to process}
                            {--transaction-id= : Check specific transaction ID}';

    protected $description = 'Check pending payments using individual jobs for better reliability';

    public function handle()
    {
        $this->info('=== IMPROVED PAYMENT STATUS CHECK ===');
        $this->info('Time: ' . now()->format('Y-m-d H:i:s'));

        // Get transactions to check
        if ($this->option('transaction-id')) {
            $transactions = Transaction::where('id', $this->option('transaction-id'))
                ->where('status', 'pending')
                ->get();
            $this->info("Checking specific transaction ID: " . $this->option('transaction-id'));
        } else {
            $transactions = Transaction::where('status', 'pending')
                ->where('created_at', '>', Carbon::now()->subHours(48))
                ->orderBy('created_at', 'asc')
                ->limit($this->option('limit'))
                ->get();
        }

        $this->info("Found {$transactions->count()} pending transactions");

        if ($transactions->count() === 0) {
            $this->info('No pending transactions found.');
            return 0;
        }

        $dispatched = 0;

        foreach ($transactions as $transaction) {
            try {
                $this->info("Processing: {$transaction->order_id} (ID: {$transaction->id})");

                if ($this->option('sync')) {
                    // Run synchronously for immediate results
                    $job = new CheckPaymentStatus($transaction->id);
                    $job->handle();
                    $this->info("✅ Processed synchronously: {$transaction->order_id}");
                } else {
                    // Dispatch to queue with delay to avoid rate limiting
                    CheckPaymentStatus::dispatch($transaction->id)->delay(now()->addSeconds($dispatched * 2));
                    $this->info("📤 Dispatched to queue: {$transaction->order_id}");
                }

                $dispatched++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to process {$transaction->order_id}: {$e->getMessage()}");

                Log::error('Command dispatch error', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($this->option('sync')) {
            $this->info("✅ Completed synchronous processing of {$dispatched} transactions");
        } else {
            $this->info("📤 Dispatched {$dispatched} jobs to queue");
            $this->info("💡 Monitor jobs with: php artisan queue:work");
        }

        return 0;
    }
}