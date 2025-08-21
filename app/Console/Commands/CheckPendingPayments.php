<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckPendingPayments extends Command
{
    protected $signature = 'payment:check-pending {--transaction-id= : Check specific transaction ID} {--verbose : Show detailed output}';
    protected $description = 'Check and update pending payment status from Midtrans';

    public function handle()
    {
        $this->info('=== STARTING PAYMENT STATUS CHECK ===');
        $this->info('Time: ' . now()->format('Y-m-d H:i:s'));

        // Setup Midtrans config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $this->info('Midtrans Config:');
        $this->info('- Server Key: ' . (config('midtrans.server_key') ? 'SET (***' . substr(config('midtrans.server_key'), -4) . ')' : 'NOT SET'));
        $this->info('- Production: ' . (config('midtrans.is_production', false) ? 'YES' : 'NO'));

        // Get transactions to check
        if ($this->option('transaction-id')) {
            $transactionsToCheck = Transaction::where('id', $this->option('transaction-id'))
                ->where('status', 'pending')
                ->get();
            $this->info("Checking specific transaction ID: " . $this->option('transaction-id'));
        } else {
            // Get pending transactions from last 48 hours (extended range)
            $transactionsToCheck = Transaction::where('status', 'pending')
                ->where('created_at', '>', Carbon::now()->subHours(48))
                ->orderBy('created_at', 'asc') // Process oldest first
                ->get();
        }

        $this->info("Found {$transactionsToCheck->count()} pending transactions to check");

        if ($transactionsToCheck->count() === 0) {
            $this->info('No pending transactions found. Exiting.');
            return 0;
        }

        $updatedCount = 0;
        $errorCount = 0;
        $unchangedCount = 0;

        foreach ($transactionsToCheck as $transactionToCheck) {
            try {
                $this->info("--- Processing Transaction #{$transactionToCheck->id} ---");
                $this->info("Order ID: {$transactionToCheck->order_id}");
                $this->info("Created: {$transactionToCheck->created_at}");
                $this->info("Amount: Rp " . number_format($transactionToCheck->amount));

                if ($this->option('verbose')) {
                    $this->info("Customer: {$transactionToCheck->name} ({$transactionToCheck->email})");
                    $this->info("Phone: {$transactionToCheck->phone}");
                }

                // Get status from Midtrans - use unique variable names
                $midtransApiResponse = MidtransTransaction::status($transactionToCheck->order_id);

                // Ensure it's an object
                if (is_array($midtransApiResponse)) {
                    $midtransApiResponse = (object) $midtransApiResponse;
                }

                $apiTransactionStatus = $midtransApiResponse->transaction_status ?? '';
                $apiPaymentType = $midtransApiResponse->payment_type ?? '';
                $apiFraudStatus = $midtransApiResponse->fraud_status ?? '';

                $this->info("Midtrans Status: {$apiTransactionStatus}");
                $this->info("Payment Type: {$apiPaymentType}");
                if ($apiFraudStatus) {
                    $this->info("Fraud Status: {$apiFraudStatus}");
                }

                // Determine new status
                $calculatedNewStatus = $this->determineTransactionStatus(
                    $apiTransactionStatus,
                    $apiPaymentType,
                    $apiFraudStatus
                );

                $previousDbStatus = $transactionToCheck->status;

                $this->info("Current DB Status: {$previousDbStatus}");
                $this->info("Calculated New Status: {$calculatedNewStatus}");

                if ($calculatedNewStatus !== $previousDbStatus) {
                    // Update database
                    $transactionToCheck->update([
                        'status' => $calculatedNewStatus,
                        'midtrans_response' => json_decode(json_encode($midtransApiResponse), true),
                        'payment_type' => $apiPaymentType ?: null,
                        'updated_at' => now()
                    ]);

                    $this->info("✅ UPDATED: {$transactionToCheck->order_id} [{$previousDbStatus} → {$calculatedNewStatus}]");
                    $updatedCount++;

                    // Log the update
                    Log::info('=== COMMAND: Transaction Status Updated ===', [
                        'transaction_id' => $transactionToCheck->id,
                        'order_id' => $transactionToCheck->order_id,
                        'old_status' => $previousDbStatus,
                        'new_status' => $calculatedNewStatus,
                        'midtrans_transaction_status' => $apiTransactionStatus,
                        'payment_type' => $apiPaymentType,
                        'updated_by' => 'console_command',
                        'updated_at' => now()
                    ]);

                    // Send email if paid
                    if ($calculatedNewStatus === 'paid') {
                        $this->info("🎫 Status is PAID - Sending ticket email...");

                        try {
                            $this->sendTicketEmail($transactionToCheck);
                            $this->info("✅ Email sent successfully to: {$transactionToCheck->email}");

                            Log::info('=== COMMAND: Email Sent ===', [
                                'transaction_id' => $transactionToCheck->id,
                                'email' => $transactionToCheck->email,
                                'sent_at' => now()
                            ]);
                        } catch (\Exception $emailException) {
                            $this->error("❌ Failed to send email: {$emailException->getMessage()}");

                            Log::error('=== COMMAND: Email Failed ===', [
                                'transaction_id' => $transactionToCheck->id,
                                'email' => $transactionToCheck->email,
                                'error' => $emailException->getMessage()
                            ]);
                        }
                    }
                } else {
                    $this->info("➡️  No change needed for: {$transactionToCheck->order_id} (remains {$previousDbStatus})");
                    $unchangedCount++;
                }

                $this->info(''); // Empty line for readability

            } catch (\Exception $processingException) {
                $this->error("❌ ERROR processing {$transactionToCheck->order_id}: {$processingException->getMessage()}");

                Log::error('=== COMMAND: Processing Error ===', [
                    'transaction_id' => $transactionToCheck->id,
                    'order_id' => $transactionToCheck->order_id,
                    'error' => $processingException->getMessage(),
                    'trace' => $processingException->getTraceAsString()
                ]);

                $errorCount++;
            }
        }

        // Summary
        $this->info('=== PROCESSING COMPLETED ===');
        $this->info("Total processed: {$transactionsToCheck->count()}");
        $this->info("Updated: {$updatedCount}");
        $this->info("Unchanged: {$unchangedCount}");
        $this->info("Errors: {$errorCount}");
        $this->info('Completed at: ' . now()->format('Y-m-d H:i:s'));

        Log::info('=== COMMAND: Batch Processing Summary ===', [
            'total_processed' => $transactionsToCheck->count(),
            'updated' => $updatedCount,
            'unchanged' => $unchangedCount,
            'errors' => $errorCount,
            'completed_at' => now()
        ]);

        return 0;
    }

    private function determineTransactionStatus($midtransTransactionStatus, $paymentType, $fraudStatus)
    {
        Log::info('=== COMMAND: Determining Status ===', [
            'midtrans_transaction_status' => $midtransTransactionStatus,
            'payment_type' => $paymentType,
            'fraud_status' => $fraudStatus
        ]);

        switch ($midtransTransactionStatus) {
            case 'capture':
                if ($paymentType == 'credit_card') {
                    return ($fraudStatus == 'challenge') ? 'pending' : 'paid';
                } else {
                    return 'paid';
                }

            case 'settlement':
                return 'paid';

            case 'pending':
                return 'pending';

            case 'deny':
            case 'failure':
                return 'failed';

            case 'expire':
                return 'expired';

            case 'cancel':
                return 'cancelled';

            default:
                Log::warning('=== COMMAND: Unknown Status ===', [
                    'unknown_status' => $midtransTransactionStatus
                ]);
                return 'pending';
        }
    }

    private function sendTicketEmail($transaction)
    {
        try {
            $pdf = PDF::loadView('auth.regular', ['transaction' => $transaction]);
            Mail::to($transaction->email)->send(new TicketMail($transaction, $pdf, 'regular'));
        } catch (\Exception $e) {
            Log::error('=== COMMAND: Send Ticket Email Error ===', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}