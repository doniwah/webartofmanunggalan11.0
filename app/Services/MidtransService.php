<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    /**
     * Create Snap Token
     */
    public function createSnapToken($params)
    {
        try {
            $snapToken = Snap::getSnapToken($params);

            Log::info('Snap token created successfully', [
                'order_id' => $params['transaction_details']['order_id'] ?? 'N/A',
                'amount' => $params['transaction_details']['gross_amount'] ?? 'N/A'
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Failed to create snap token', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            throw $e;
        }
    }

    /**
     * Handle Midtrans Notification
     */
    public function handleNotification()
    {
        try {
            $notification = new Notification();

            $transaction_status = $notification->transaction_status;
            $payment_type = $notification->payment_type;
            $order_id = $notification->order_id;
            $fraud_status = $notification->fraud_status ?? null;

            Log::info('Midtrans notification received', [
                'order_id' => $order_id,
                'transaction_status' => $transaction_status,
                'payment_type' => $payment_type,
                'fraud_status' => $fraud_status
            ]);

            return [
                'order_id' => $order_id,
                'transaction_status' => $transaction_status,
                'payment_type' => $payment_type,
                'fraud_status' => $fraud_status,
                'notification' => $notification
            ];
        } catch (\Exception $e) {
            Log::error('Failed to handle notification', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);

            Log::info('Transaction status retrieved', [
                'order_id' => $orderId,
                'status' => $status
            ]);

            return $status;
        } catch (\Exception $e) {
            Log::error('Failed to get transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Cancel transaction
     */
    public function cancelTransaction($orderId)
    {
        try {
            $result = \Midtrans\Transaction::cancel($orderId);

            Log::info('Transaction cancelled', [
                'order_id' => $orderId,
                'result' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to cancel transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Create standard transaction parameters
     */
    public function createTransactionParams($orderId, $grossAmount, $customerDetails, $itemDetails = null)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $grossAmount,
            ],
            'customer_details' => $customerDetails,
        ];

        if ($itemDetails) {
            $params['item_details'] = $itemDetails;
        }

        // Set expiry time (24 hours)
        $params['expiry'] = [
            'start_time' => date('Y-m-d H:i:s O'),
            'unit' => 'hours',
            'duration' => 24
        ];

        return $params;
    }

    /**
     * Validate Midtrans signature
     */
    public function validateSignature($orderId, $statusCode, $grossAmount, $signature)
    {
        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));

        return $mySignature === $signature;
    }

    /**
     * Format currency to IDR
     */
    public function formatCurrency($amount)
    {
        return 'Rp. ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Map Midtrans status to application status
     */
    public function mapTransactionStatus($midtransStatus, $paymentType = null, $fraudStatus = null)
    {
        switch ($midtransStatus) {
            case 'capture':
                if ($paymentType === 'credit_card') {
                    return $fraudStatus === 'challenge' ? 'pending' : 'paid';
                }
                return 'paid';

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
                return 'unknown';
        }
    }

    /**
     * Create item details for ticket purchase
     */
    public function createTicketItemDetails($quantity, $unitPrice, $adminFee = 2500)
    {
        return [
            [
                'id' => 'ticket-regular',
                'price' => (int) $unitPrice,
                'quantity' => (int) $quantity,
                'name' => 'Tiket Reguler Event',
            ],
            [
                'id' => 'admin-fee',
                'price' => (int) $adminFee,
                'quantity' => 1,
                'name' => 'Biaya Admin',
            ]
        ];
    }

    /**
     * Create customer details
     */
    public function createCustomerDetails($name, $email, $phone)
    {
        return [
            'first_name' => $name,
            'email' => $email,
            'phone' => $phone,
        ];
    }
}