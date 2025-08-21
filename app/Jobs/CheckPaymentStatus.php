<?php
// File: app/Jobs/CheckPaymentStatus.php
// Buat job untuk handle payment status check secara individual

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Transaction as MidtransTransaction;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckPaymentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;

    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function handle()
    {
        try {
            $transaction = Transaction::find($this->transactionId);

            if (!$transaction || $transaction->status !== 'pending') {
                Log::info('=== JOB: Transaction not found or not pending ===', [
                    'transaction_id' => $this->transactionId,
                    'status' => $transaction->status ?? 'not_found'
                ]);
                return;
            }

            Log::info('=== JOB: Processing payment status check ===', [
                'transaction_id' => $this->transactionId,
                'order_id' => $transaction->order_id
            ]);

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Get status from Midtrans
            $midtransResponse = MidtransTransaction::status($transaction->order_id);

            if (is_array($midtransResponse)) {
                $midtransResponse = (object) $midtransResponse;
            }

            $newStatus = $this->determineTransactionStatus(
                $midtransResponse->transaction_status ?? '',
                $midtransResponse->payment_type ?? '',
                $midtransResponse->fraud_status ?? ''
            );

            $oldStatus = $transaction->status;

            if ($newStatus !== $oldStatus) {
                $transaction->update([
                    'status' => $newStatus,
                    'midtrans_response' => json_decode(json_encode($midtransResponse), true),
                    'payment_type' => $midtransResponse->payment_type ?? null,
                    'updated_at' => now()
                ]);

                Log::info('=== JOB: Status updated ===', [
                    'transaction_id' => $this->transactionId,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                // Send email if paid
                if ($newStatus === 'paid') {
                    try {
                        $pdf = PDF::loadView('auth.regular', ['transaction' => $transaction]);
                        Mail::to($transaction->email)->send(new TicketMail($transaction, $pdf, 'regular'));

                        Log::info('=== JOB: Email sent ===', [
                            'transaction_id' => $this->transactionId
                        ]);
                    } catch (\Exception $e) {
                        Log::error('=== JOB: Email failed ===', [
                            'transaction_id' => $this->transactionId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('=== JOB: Payment status check failed ===', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Retry job if failed (up to 3 times)
            if ($this->attempts() < 3) {
                $this->release(30); // Retry after 30 seconds
            }
        }
    }

    private function determineTransactionStatus($transactionStatus, $paymentType, $fraudStatus)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($paymentType == 'credit_card' && $fraudStatus == 'challenge') ? 'pending' : 'paid';
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
                return 'pending';
        }
    }
}