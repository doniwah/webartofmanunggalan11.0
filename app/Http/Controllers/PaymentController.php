<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MidtransService;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;

        // Setup Midtrans Config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function silver($ticket_id = null)
    {
        try {
            if (!$ticket_id || !is_numeric($ticket_id)) {
                return redirect()->route('listticket')->with('error', 'ID tiket tidak valid');
            }

            $selectedTicket = \App\Models\Ticket::with('ticket_benefit')->find($ticket_id);

            if (!$selectedTicket) {
                return redirect()->route('listticket')->with('error', 'Tiket tidak ditemukan');
            }

            if ($selectedTicket->quantity <= 0) {
                return redirect()->back()->with('error', 'Tiket tidak tersedia');
            }

            // Pass the ticket price to the view
            return view('auth.checkout', [
                'selectedTicket' => $selectedTicket,
                'ticketPrice' => $selectedTicket->price
            ]);
        } catch (\Exception $e) {
            Log::error('Error in silver method: ' . $e->getMessage());
            return redirect()->route('listticket')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showPaymentPage()
    {
        return view('auth.payment');
    }

    public function createPayment(Request $request)
{
    try {
        Log::info('Create Payment Request', $request->all());

        // Validate request - PERBAIKI VALIDASI
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:10000',
            'quantity' => 'required|integer|min:1',
            'force_new' => 'boolean',
            'ticket_id' => 'required|integer|min:1' // Perbaiki validasi: integer min 1, bukan exists
        ]);

        // PERBAIKI: Cari ticket berdasarkan kolom yang benar
        $ticket = \App\Models\Ticket::where('idTicket', $request->ticket_id)->first();
        
        // Log untuk debugging
        Log::info('Ticket Search Debug', [
            'requested_ticket_id' => $request->ticket_id,
            'ticket_found' => $ticket ? 'YES' : 'NO',
            'ticket_data' => $ticket ? $ticket->toArray() : null
        ]);
        
        if (!$ticket) {
            Log::error('Ticket not found', [
                'ticket_id' => $request->ticket_id,
                'all_tickets' => \App\Models\Ticket::select('idTicket', 'name')->get()->toArray()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket dengan ID ' . $request->ticket_id . ' tidak ditemukan'
            ], 404);
        }
        
        if ($ticket->quantity < $request->quantity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jumlah tiket yang tersedia tidak mencukupi. Tersedia: ' . $ticket->quantity
            ], 400);
        }

        // Jika force_new true, batalkan transaksi pending sebelumnya
        if ($request->force_new) {
            Transaction::where('phone', $request->phone)
                ->where('name', $request->name)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        } else {
            // Check existing transaction
            $existingTransaction = Transaction::where('phone', $request->phone)
                ->where('name', $request->name)
                ->where('status', 'pending')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($existingTransaction) {
                Log::info('Found existing transaction', ['transaction_id' => $existingTransaction->id]);

                return response()->json([
                    'status' => 'success',
                    'snap_token' => $existingTransaction->snap_token,
                    'transaction_id' => $existingTransaction->id,
                    'expires_at' => $existingTransaction->expires_at->toISOString(),
                    'message' => 'Melanjutkan pembayaran yang sudah ada'
                ]);
            }
        }

        // Create new transaction - TAMBAHKAN ticket_id ke database
        $orderId = 'AOM11-' . time() . '-' . strtoupper(Str::random(6));

        $transaction = Transaction::create([
            'order_id' => $orderId,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'quantity' => $request->quantity ?? 1,
            'amount' => $request->amount,
            'admin_fee' => 2500,
            'status' => 'pending',
            'ticket_id' => $ticket->idTicket, // TAMBAHKAN INI - simpan ticket_id
            'expires_at' => Carbon::now()->addHours(24)
        ]);

        Log::info('Created new transaction', [
            'transaction_id' => $transaction->id, 
            'order_id' => $orderId,
            'ticket_id' => $ticket->idTicket,
            'ticket_name' => $ticket->name
        ]);

        // Prepare Midtrans parameters
        $unitPrice = ($request->amount - 2500) / $request->quantity;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $request->amount,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ],
            'item_details' => [
                [
                    'id' => 'ticket-' . $ticket->idTicket,
                    'price' => (int) $unitPrice,
                    'quantity' => (int) $request->quantity,
                    'name' => $ticket->name, // Gunakan nama tiket yang sebenarnya
                ],
                [
                    'id' => 'admin-fee',
                    'price' => 2500,
                    'quantity' => 1,
                    'name' => 'Biaya Admin',
                ]
            ],
            'expiry' => [
                'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                'unit' => 'hours',
                'duration' => 24
            ],
            'callbacks' => [
                'finish' => url('/payment-success/' . $transaction->id . '?clean=1'),
                'unfinish' => url('/payment-silver'),
                'error' => url('/payment-silver'),
            ]
        ];

        Log::info('Midtrans Parameters', $params);

        // Get snap token from Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Update transaction with snap token
        $transaction->update([
            'snap_token' => $snapToken
        ]);

        Log::info('Snap token generated successfully', [
            'token' => substr($snapToken, 0, 20) . '...',
            'transaction_id' => $transaction->id
        ]);

        return response()->json([
            'status' => 'success',
            'snap_token' => $snapToken,
            'transaction_id' => $transaction->id,
            'expires_at' => $transaction->expires_at->toISOString(),
            'message' => 'Transaksi berhasil dibuat'
        ]);
    } catch (\Exception $e) {
        Log::error('Create Payment Error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        if (isset($transaction) && $transaction) {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
        ], 500);
    }
}

    public function clearPreviousTransactions(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string'
            ]);

            // Batalkan semua transaksi pending untuk user ini
            $cancelled = Transaction::where('phone', $request->phone)
                ->where('name', $request->name)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            return response()->json([
                'status' => 'success',
                'cancelled_count' => $cancelled,
                'message' => "Membatalkan {$cancelled} transaksi pending"
            ]);
        } catch (\Exception $e) {
            Log::error('Clear Previous Transactions Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan transaksi sebelumnya'
            ], 500);
        }
    }

    public function checkTransactionStatusById($id)
    {
        try {
            $transaction = Transaction::find($id);

            if (!$transaction) {
                return response()->json([
                    'status' => 'not_found',
                    'is_expired' => true
                ], 404);
            }

            $isExpired = $transaction->expires_at < now();

            // Update status jika sudah expired tapi masih pending
            if ($isExpired && $transaction->status === 'pending') {
                $transaction->update(['status' => 'expired']);
            }

            return response()->json([
                'status' => $transaction->status,
                'is_expired' => $isExpired,
                'expires_at' => $transaction->expires_at,
                'payment_status' => $transaction->status
            ]);
        } catch (\Exception $e) {
            Log::error('Check Transaction Status By ID Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'is_expired' => true
            ], 500);
        }
    }


    public function checkTransactionStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string'
            ]);

            // Cari transaksi yang masih aktif (pending dan belum expired)
            $activeTransaction = Transaction::where('phone', $request->phone)
                ->where('name', $request->name)
                ->where('status', 'pending')
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($activeTransaction) {
                return response()->json([
                    'status' => 'active',
                    'payment_status' => 'pending',
                    'transaction_id' => $activeTransaction->id,
                    'snap_token' => $activeTransaction->snap_token,
                    'expires_at' => $activeTransaction->expires_at->toISOString(),
                    'message' => 'Ada transaksi yang masih pending'
                ]);
            }

            // Cek juga apakah ada transaksi yang sudah berhasil
            $paidTransaction = Transaction::where('phone', $request->phone)
                ->where('name', $request->name)
                ->where('status', 'paid')
                ->whereDate('created_at', '>=', Carbon::now()->subDays(1)) // Transaksi dalam 1 hari terakhir
                ->first();

            if ($paidTransaction) {
                return response()->json([
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'transaction_id' => $paidTransaction->id,
                    'message' => 'Transaksi sudah berhasil dibayar'
                ]);
            }

            return response()->json([
                'status' => 'none',
                'message' => 'Tidak ada transaksi aktif'
            ]);
        } catch (\Exception $e) {
            Log::error('Check Transaction Status Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengecek status transaksi'
            ], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        // Log semua data yang masuk dari Midtrans
        Log::info('=== MIDTRANS NOTIFICATION RECEIVED ===', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);

        try {
            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Buat object notifikasi dari Midtrans
            $notif = new \Midtrans\Notification();

            Log::info('=== NOTIFICATION OBJECT CREATED ===', [
                'order_id' => $notif->order_id,
                'transaction_status' => $notif->transaction_status,
                'payment_type' => $notif->payment_type,
                'gross_amount' => $notif->gross_amount,
                'signature_key' => $notif->signature_key ?? 'not_provided'
            ]);

            // Cari transaksi berdasarkan order_id
            $transaction = Transaction::where('order_id', $notif->order_id)->first();

            if (!$transaction) {
                Log::error('=== TRANSACTION NOT FOUND ===', [
                    'order_id' => $notif->order_id
                ]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            Log::info('=== TRANSACTION FOUND ===', [
                'transaction_id' => $transaction->id,
                'current_status' => $transaction->status,
                'order_id' => $transaction->order_id
            ]);

            // Tentukan status baru
            $newStatus = $this->determineTransactionStatus(
                $notif->transaction_status,
                $notif->payment_type,
                $notif->fraud_status ?? null
            );

            Log::info('=== STATUS DETERMINATION ===', [
                'old_status' => $transaction->status,
                'new_status' => $newStatus,
                'midtrans_transaction_status' => $notif->transaction_status
            ]);

            // Update status jika berbeda
            if ($transaction->status !== $newStatus) {
                $transaction->update([
                    'status' => $newStatus,
                    'payment_type' => $notif->payment_type,
                    'midtrans_response' => json_decode(json_encode($notif->getResponse()), true),
                    'updated_at' => now()
                ]);

                Log::info('=== TRANSACTION UPDATED ===', [
                    'transaction_id' => $transaction->id,
                    'updated_to_status' => $newStatus
                ]);

                // Kirim email jika status paid
                if ($newStatus === 'paid') {

                    $this->reduceTicketQuantity($transaction);
                    try {
                        $this->sendTicketEmail($transaction);
                        Log::info('=== EMAIL SENT SUCCESSFULLY ===', [
                            'transaction_id' => $transaction->id,
                            'email' => $transaction->email
                        ]);
                    } catch (\Exception $e) {
                        Log::error('=== EMAIL SENDING FAILED ===', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            } else {
                Log::info('=== NO UPDATE NEEDED ===', [
                    'transaction_id' => $transaction->id,
                    'status' => $transaction->status
                ]);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('=== NOTIFICATION PROCESSING ERROR ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    private function reduceTicketQuantity($transaction)
{
    try {
        // Gunakan ticket_id yang disimpan di transaction
        if ($transaction->ticket_id) {
            // Cari berdasarkan idTicket, bukan id
            $ticket = \App\Models\Ticket::where('idTicket', $transaction->ticket_id)->first();
            
            if ($ticket) {
                $oldQuantity = $ticket->quantity;
                $ticket->decrement('quantity', $transaction->quantity);
                
                Log::info('=== TICKET QUANTITY REDUCED ===', [
                    'ticket_id' => $ticket->idTicket,
                    'ticket_name' => $ticket->name,
                    'quantity_reduced' => $transaction->quantity,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $ticket->fresh()->quantity
                ]);
            } else {
                Log::error('=== TICKET NOT FOUND FOR QUANTITY REDUCTION ===', [
                    'ticket_id' => $transaction->ticket_id,
                    'transaction_id' => $transaction->id
                ]);
            }
        } else {
            Log::error('=== NO TICKET_ID IN TRANSACTION ===', [
                'transaction_id' => $transaction->id
            ]);
        }
    } catch (\Exception $e) {
        Log::error('=== FAILED TO REDUCE TICKET QUANTITY ===', [
            'transaction_id' => $transaction->id,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}


    public function manualStatusCheck($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            // Check status dari Midtrans
            $status = \Midtrans\Transaction::status($transaction->order_id);

            Log::info('Manual status check', [
                'order_id' => $transaction->order_id,
                'midtrans_status' => $status->transaction_status,
                'current_db_status' => $transaction->status
            ]);

            // Tentukan status baru
            $newStatus = $this->determineTransactionStatus(
                $status->transaction_status,
                $status->payment_type ?? '',
                $status->fraud_status ?? ''
            );

            // Update jika berbeda
            if ($newStatus !== $transaction->status) {
                $oldStatus = $transaction->status;

                $transaction->update([
                    'status' => $newStatus,
                    'midtrans_response' => json_decode(json_encode($status), true),
                    'payment_type' => $status->payment_type ?? null,
                    'updated_at' => now()
                ]);

                // Kirim email jika paid
                if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                    try {
                        $this->sendTicketEmail($transaction);
                    } catch (\Exception $e) {
                        Log::error('Email sending failed', ['error' => $e->getMessage()]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'updated' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'updated' => false,
                'message' => 'Status already up to date'
            ]);
        } catch (\Exception $e) {
            Log::error('Manual status check error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function determineTransactionStatus($transactionStatus, $paymentType, $fraudStatus)
    {
        Log::info('Determining status', [
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'fraud_status' => $fraudStatus
        ]);

        switch ($transactionStatus) {
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
                Log::warning('Unknown transaction status', ['status' => $transactionStatus]);
                return 'pending';
        }
    }

    public function checkAllPendingTransactions()
    {
        try {
            // Ambil transaksi pending yang lebih spesifik
            $pendingTransactions = Transaction::where('status', 'pending')
                ->where('created_at', '>', Carbon::now()->subHours(48)) // Extend to 48 hours for thorough check
                ->orderBy('created_at', 'asc') // Process older transactions first
                ->get();

            Log::info('=== CHECKING ALL PENDING TRANSACTIONS ===', [
                'total_found' => $pendingTransactions->count(),
                'start_time' => now()
            ]);

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($pendingTransactions as $dbTransaction) {
                try {
                    Log::info('=== PROCESSING TRANSACTION ===', [
                        'transaction_id' => $dbTransaction->id,
                        'order_id' => $dbTransaction->order_id,
                        'current_status' => $dbTransaction->status,
                        'created_at' => $dbTransaction->created_at,
                        'amount' => $dbTransaction->amount
                    ]);

                    // Setup Midtrans config untuk setiap iterasi (penting!)
                    Config::$serverKey = config('midtrans.server_key');
                    Config::$isProduction = config('midtrans.is_production', false);
                    Config::$isSanitized = true;
                    Config::$is3ds = true;

                    // Get status from Midtrans
                    $midtransStatusResponse = \Midtrans\Transaction::status($dbTransaction->order_id);

                    // Log raw response untuk debugging
                    Log::info('=== MIDTRANS RESPONSE ===', [
                        'order_id' => $dbTransaction->order_id,
                        'raw_response' => $midtransStatusResponse,
                        'response_type' => gettype($midtransStatusResponse)
                    ]);

                    // Ensure response is object
                    if (is_array($midtransStatusResponse)) {
                        $midtransStatusResponse = (object) $midtransStatusResponse;
                    }

                    // Extract status information
                    $midtransTransactionStatus = $midtransStatusResponse->transaction_status ?? '';
                    $midtransPaymentType = $midtransStatusResponse->payment_type ?? '';
                    $midtransFraudStatus = $midtransStatusResponse->fraud_status ?? '';

                    Log::info('=== EXTRACTED STATUS INFO ===', [
                        'order_id' => $dbTransaction->order_id,
                        'midtrans_transaction_status' => $midtransTransactionStatus,
                        'midtrans_payment_type' => $midtransPaymentType,
                        'midtrans_fraud_status' => $midtransFraudStatus
                    ]);

                    // Determine new status
                    $determinedNewStatus = $this->determineTransactionStatus(
                        $midtransTransactionStatus,
                        $midtransPaymentType,
                        $midtransFraudStatus
                    );

                    $currentDbStatus = $dbTransaction->status;

                    Log::info('=== STATUS COMPARISON ===', [
                        'order_id' => $dbTransaction->order_id,
                        'current_db_status' => $currentDbStatus,
                        'determined_new_status' => $determinedNewStatus,
                        'needs_update' => $determinedNewStatus !== $currentDbStatus
                    ]);

                    if ($determinedNewStatus !== $currentDbStatus) {
                        // Update transaction
                        $dbTransaction->update([
                            'status' => $determinedNewStatus,
                            'midtrans_response' => json_decode(json_encode($midtransStatusResponse), true),
                            'payment_type' => $midtransPaymentType ?: null,
                            'updated_at' => now()
                        ]);

                        Log::info('=== TRANSACTION UPDATED ===', [
                            'transaction_id' => $dbTransaction->id,
                            'order_id' => $dbTransaction->order_id,
                            'status_changed_from' => $currentDbStatus,
                            'status_changed_to' => $determinedNewStatus,
                            'updated_at' => now()
                        ]);

                        $results[] = [
                            'transaction_id' => $dbTransaction->id,
                            'order_id' => $dbTransaction->order_id,
                            'old_status' => $currentDbStatus,
                            'new_status' => $determinedNewStatus,
                            'updated' => true,
                            'midtrans_status' => $midtransTransactionStatus
                        ];

                        // Send email if paid
                        if ($determinedNewStatus === 'paid') {
                            Log::info('=== SENDING TICKET EMAIL ===', [
                                'transaction_id' => $dbTransaction->id,
                                'email' => $dbTransaction->email
                            ]);

                            try {
                                $this->sendTicketEmail($dbTransaction);
                                Log::info('=== EMAIL SENT SUCCESSFULLY ===', [
                                    'transaction_id' => $dbTransaction->id
                                ]);
                            } catch (\Exception $emailError) {
                                Log::error('=== EMAIL SENDING FAILED ===', [
                                    'transaction_id' => $dbTransaction->id,
                                    'email_error' => $emailError->getMessage()
                                ]);
                            }
                        }

                        $successCount++;
                    } else {
                        Log::info('=== NO UPDATE NEEDED ===', [
                            'order_id' => $dbTransaction->order_id,
                            'status' => $currentDbStatus
                        ]);

                        $results[] = [
                            'transaction_id' => $dbTransaction->id,
                            'order_id' => $dbTransaction->order_id,
                            'status' => $currentDbStatus,
                            'updated' => false,
                            'midtrans_status' => $midtransTransactionStatus
                        ];
                    }
                } catch (\Exception $individualError) {
                    Log::error('=== INDIVIDUAL TRANSACTION ERROR ===', [
                        'transaction_id' => $dbTransaction->id,
                        'order_id' => $dbTransaction->order_id,
                        'error_message' => $individualError->getMessage(),
                        'error_trace' => $individualError->getTraceAsString()
                    ]);

                    $results[] = [
                        'transaction_id' => $dbTransaction->id,
                        'order_id' => $dbTransaction->order_id,
                        'error' => $individualError->getMessage(),
                        'updated' => false
                    ];

                    $errorCount++;
                }
            }

            Log::info('=== BATCH PROCESSING COMPLETED ===', [
                'total_processed' => count($results),
                'successful_updates' => $successCount,
                'errors' => $errorCount,
                'completion_time' => now()
            ]);

            return response()->json([
                'status' => 'completed',
                'processed_count' => count($results),
                'successful_updates' => $successCount,
                'errors' => $errorCount,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('=== BATCH PROCESSING FAILED ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function sendTicketEmail($transaction)
    {
        try {
            $pdf = PDF::loadView('auth.regular', ['transaction' => $transaction]);
            Mail::to($transaction->email)->send(new TicketMail($transaction, $pdf, 'regular'));
        } catch (\Exception $e) {
            Log::error('Send ticket email error', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function showPayment($transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);

            if ($transaction->status === 'paid') {
                return redirect()->route('payment.success', $transactionId);
            } else if ($transaction->isExpired() || $transaction->status === 'expired') {
                return redirect()->route('payment.expired');
            } else if (!in_array($transaction->status, ['pending'])) {
                return redirect()->route('payment.silver');
            }

            return view('payment.show', [
                'transaction' => $transaction,
                'snapToken' => $transaction->snap_token,
                'quantity' => $transaction->quantity,
                'basePrice' => ($transaction->amount - $transaction->admin_fee) / $transaction->quantity,
                'totalAmount' => $transaction->amount,
                'expiresAt' => $transaction->expires_at
            ]);
        } catch (\Exception $e) {
            Log::error('Show Payment Error', ['error' => $e->getMessage()]);
            return redirect()->route('payment.silver')->with('error', 'Transaksi tidak ditemukan');
        }
    }

    public function paymentSuccess($transactionId)
{
    try {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Log untuk debugging
        Log::info('=== PAYMENT SUCCESS PAGE ACCESSED ===', [
            'transaction_id' => $transactionId,
            'current_status' => $transaction->status,
            'request_params' => request()->all(),
            'user_agent' => request()->userAgent()
        ]);

        // Cek parameter dari URL untuk handling khusus
        $requestParams = request()->all();
        
        // Jika ada parameter verified=1, langsung tampilkan success
        if (isset($requestParams['verified']) && $requestParams['verified'] == '1') {
            Log::info('=== VERIFIED SUCCESS ACCESS ===', ['transaction_id' => $transactionId]);
            return view('auth.success', compact('transaction'));
        }
        
        // Jika status sudah paid, tampilkan halaman success
        if ($transaction->status === 'paid') {
            Log::info('=== STATUS ALREADY PAID ===', ['transaction_id' => $transactionId]);
            return view('auth.success', compact('transaction'));
        }
        
        // Jika ada parameter status=processing, cek status dari Midtrans dulu
        if (isset($requestParams['status']) && $requestParams['status'] === 'processing') {
            Log::info('=== PROCESSING STATUS - CHECKING MIDTRANS ===', ['transaction_id' => $transactionId]);
            
            try {
                // Setup Midtrans config
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production', false);
                
                // Check status dari Midtrans
                $midtransResponse = \Midtrans\Transaction::status($transaction->order_id);
                
                if (is_array($midtransResponse)) {
                    $midtransResponse = (object) $midtransResponse;
                }
                
                $newStatus = $this->determineTransactionStatus(
                    $midtransResponse->transaction_status ?? '',
                    $midtransResponse->payment_type ?? '',
                    $midtransResponse->fraud_status ?? ''
                );
                
                // Update status jika berbeda
                if ($newStatus !== $transaction->status) {
                    $transaction->update([
                        'status' => $newStatus,
                        'midtrans_response' => json_decode(json_encode($midtransResponse), true),
                        'payment_type' => $midtransResponse->payment_type ?? null
                    ]);
                    
                    Log::info('=== STATUS UPDATED FROM MIDTRANS CHECK ===', [
                        'transaction_id' => $transactionId,
                        'old_status' => $transaction->status,
                        'new_status' => $newStatus
                    ]);
                    
                    // Send email if paid
                    if ($newStatus === 'paid') {
                        try {
                            $this->reduceTicketQuantity($transaction);
                            $this->sendTicketEmail($transaction);
                            Log::info('=== EMAIL SENT AFTER STATUS UPDATE ===', ['transaction_id' => $transactionId]);
                        } catch (\Exception $e) {
                            Log::error('=== EMAIL FAILED AFTER STATUS UPDATE ===', [
                                'transaction_id' => $transactionId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    
                    // Refresh transaction object
                    $transaction->refresh();
                }
                
                // Setelah update, cek lagi statusnya
                if ($transaction->status === 'paid') {
                    return view('auth.success', compact('transaction'));
                }
                
            } catch (\Exception $e) {
                Log::error('=== ERROR CHECKING MIDTRANS ON SUCCESS PAGE ===', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Handle different statuses
        if ($transaction->status === 'pending') {
            // Cek apakah transaksi sudah expired
            if ($transaction->isExpired()) {
                Log::info('=== TRANSACTION EXPIRED ===', ['transaction_id' => $transactionId]);
                $transaction->update(['status' => 'expired']);
                return view('auth.expired', compact('transaction'));
            }
            
            // Jika masih pending dan belum expired, tampilkan halaman pending
            Log::info('=== TRANSACTION STILL PENDING ===', ['transaction_id' => $transactionId]);
            return view('auth.pending', compact('transaction'))->with([
                'message' => 'Pembayaran Anda sedang diproses. Halaman akan otomatis refresh.',
                'refresh_interval' => 10 // refresh setiap 10 detik
            ]);
            
        } elseif ($transaction->status === 'expired') {
            Log::info('=== TRANSACTION EXPIRED ===', ['transaction_id' => $transactionId]);
            return view('auth.expired', compact('transaction'));
            
        } elseif (in_array($transaction->status, ['failed', 'cancelled'])) {
            Log::info('=== TRANSACTION FAILED/CANCELLED ===', [
                'transaction_id' => $transactionId,
                'status' => $transaction->status
            ]);
            return redirect()->route('payment.silver', ['ticket_id' => $transaction->ticket_id])
                ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
        }
        
        // Default: redirect ke checkout jika status tidak dikenal
        Log::warning('=== UNKNOWN STATUS ON SUCCESS PAGE ===', [
            'transaction_id' => $transactionId,
            'status' => $transaction->status
        ]);
        return redirect()->route('payment.silver', ['ticket_id' => $transaction->ticket_id])
            ->with('info', 'Status pembayaran tidak dikenal. Silakan coba lagi.');
        
    } catch (\Exception $e) {
        Log::error('=== PAYMENT SUCCESS PAGE ERROR ===', [
            'transaction_id' => $transactionId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('listticket')->with('error', 'Transaksi tidak ditemukan');
    }
}

    public function refreshTransactionStatus($transactionId)
{
    try {
        $transaction = Transaction::findOrFail($transactionId);
        
        // Setup Midtrans config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        
        // Check status dari Midtrans
        $midtransResponse = \Midtrans\Transaction::status($transaction->order_id);
        
        if (is_array($midtransResponse)) {
            $midtransResponse = (object) $midtransResponse;
        }
        
        $newStatus = $this->determineTransactionStatus(
            $midtransResponse->transaction_status ?? '',
            $midtransResponse->payment_type ?? '',
            $midtransResponse->fraud_status ?? ''
        );
        
        $oldStatus = $transaction->status;
        
        // Update jika berbeda
        if ($newStatus !== $oldStatus) {
            $transaction->update([
                'status' => $newStatus,
                'midtrans_response' => json_decode(json_encode($midtransResponse), true),
                'payment_type' => $midtransResponse->payment_type ?? null
            ]);
            
            // Send email if paid
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                try {
                    $this->reduceTicketQuantity($transaction);
                    $this->sendTicketEmail($transaction);
                } catch (\Exception $e) {
                    Log::error('Email failed on refresh', ['error' => $e->getMessage()]);
                }
            }
        }
        
        return response()->json([
            'status' => 'success',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'transaction' => [
                'id' => $transaction->id,
                'order_id' => $transaction->order_id,
                'status' => $newStatus,
                'can_show_success' => $newStatus === 'paid'
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Refresh transaction status error', [
            'transaction_id' => $transactionId,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function paymentExpired()
    {
        return view('auth.expired');
    }

    public function getTransactionById($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['status' => 'not_found'], 404);
            }

            return response()->json([
                'status' => 'found',
                'transaction' => [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'status' => $transaction->status,
                    'can_pay' => $transaction->status === 'pending' && !$transaction->isExpired()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function cancelPayment($transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);

            if ($transaction->status === 'pending') {
                $transaction->update(['status' => 'cancelled']);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction cancelled successfully'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel this transaction'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Cancel Payment Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Clean expired transactions (dapat dipanggil via cron job)
     */
    public function cleanExpiredTransactions()
    {
        try {
            $expired = Transaction::where('status', 'pending')
                ->where('expires_at', '<', Carbon::now())
                ->get();

            foreach ($expired as $transaction) {
                $transaction->update(['status' => 'expired']);
            }

            return response()->json([
                'status' => 'success',
                'message' => "Cleaned {$expired->count()} expired transactions"
            ]);
        } catch (\Exception $e) {
            Log::error('Clean Expired Transactions Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Get payment statistics (optional)
     */
    public function getPaymentStats()
    {
        try {
            $stats = [
                'pending' => Transaction::where('status', 'pending')->count(),
                'paid' => Transaction::where('status', 'paid')->count(),
                'expired' => Transaction::where('status', 'expired')->count(),
                'cancelled' => Transaction::where('status', 'cancelled')->count(),
                'failed' => Transaction::where('status', 'failed')->count(),
                'total_revenue' => Transaction::where('status', 'paid')->sum('amount'),
                'today_transactions' => Transaction::whereDate('created_at', Carbon::today())->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Get Payment Stats Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    public function downloadTicket($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->status !== 'paid') {
            return redirect()->back()->with('error', 'Tiket hanya bisa diunduh setelah pembayaran berhasil');
        }

        $pdf = PDF::loadView('auth.regular', ['transaction' => $transaction]);

        return $pdf->download('Tiket-AOM11-' . $transaction->order_id . '.pdf');
    }

    public function checkAndUpdateTransactionStatus($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            // Check status dari Midtrans
            $status = \Midtrans\Transaction::status($transaction->order_id);

            Log::info('Manual status check result', [
                'order_id' => $transaction->order_id,
                'midtrans_status' => is_object($status) ? $status->transaction_status : (is_array($status) && isset($status['transaction_status']) ? $status['transaction_status'] : null),
                'current_db_status' => $transaction->status
            ]);

            // Update status berdasarkan response Midtrans
            // Handle both object and array response from Midtrans
            if (is_object($status)) {
                $transactionStatus = $status->transaction_status ?? '';
                $paymentType = $status->payment_type ?? '';
                $fraudStatus = $status->fraud_status ?? '';
            } elseif (is_array($status)) {
                $transactionStatus = $status['transaction_status'] ?? '';
                $paymentType = $status['payment_type'] ?? '';
                $fraudStatus = $status['fraud_status'] ?? '';
            } else {
                $transactionStatus = '';
                $paymentType = '';
                $fraudStatus = '';
            }

            $newStatus = $this->determineTransactionStatus(
                $transactionStatus,
                $paymentType,
                $fraudStatus
            );

            if ($newStatus !== $transaction->status) {
                $transaction->update([
                    'status' => $newStatus,
                    'midtrans_response' => json_decode(json_encode($status), true),
                    'payment_type' => $status->payment_type ?? null
                ]);

                // Send email if paid
                if ($newStatus === 'paid') {
                    $this->sendTicketEmail($transaction);
                }
            }

            return response()->json([
                'status' => 'success',
                'old_status' => $transaction->status,
                'new_status' => $newStatus,
                'midtrans_response' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Manual status check error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTransactionFromFrontend(Request $request)
    {
        try {
            $validated = $request->validate([
                'transaction_id' => 'required|integer',
                'payment_result' => 'required|array'
            ]);

            $transaction = Transaction::find($request->transaction_id);

            if (!$transaction) {
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            // Verifikasi dengan Midtrans
            $this->checkAndUpdateTransactionStatus($transaction->id);

            // Refresh transaction data
            $transaction->refresh();

            return response()->json([
                'status' => 'success',
                'transaction_status' => $transaction->status,
                'message' => 'Transaction status updated'
            ]);
        } catch (\Exception $e) {
            Log::error('Update transaction from frontend error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function debugPayment($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            // Check status dari Midtrans API
            $midtransStatus = null;
            $midtransError = null;

            try {
                $midtransStatus = \Midtrans\Transaction::status($transaction->order_id);
            } catch (\Exception $e) {
                $midtransError = $e->getMessage();
            }

            return response()->json([
                'transaction_info' => [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'current_status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                    'expires_at' => $transaction->expires_at,
                    'amount' => $transaction->amount,
                    'snap_token' => $transaction->snap_token ? 'EXISTS' : 'NULL'
                ],
                'midtrans_config' => [
                    'server_key' => config('midtrans.server_key') ? 'EXISTS (***' . substr(config('midtrans.server_key'), -4) . ')' : 'NOT SET',
                    'client_key' => config('midtrans.client_key') ? 'EXISTS' : 'NOT SET',
                    'is_production' => config('midtrans.is_production', false),
                    'notification_url' => url('/payment-notification')
                ],
                'midtrans_status' => $midtransStatus,
                'midtrans_error' => $midtransError,
                'last_midtrans_response' => $transaction->midtrans_response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function testMidtransConfig()
    {
        try {
            return response()->json([
                'config_status' => [
                    'server_key' => !empty(config('midtrans.server_key')),
                    'client_key' => !empty(config('midtrans.client_key')),
                    'is_production' => config('midtrans.is_production', false),
                    'notification_url' => url('/payment-notification')
                ],
                'env_check' => [
                    'MIDTRANS_SERVER_KEY' => !empty(env('MIDTRANS_SERVER_KEY')),
                    'MIDTRANS_CLIENT_KEY' => !empty(env('MIDTRANS_CLIENT_KEY')),
                    'MIDTRANS_IS_PRODUCTION' => env('MIDTRANS_IS_PRODUCTION', 'false')
                ],
                'php_extensions' => [
                    'curl' => extension_loaded('curl'),
                    'json' => extension_loaded('json'),
                    'openssl' => extension_loaded('openssl')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Perbaiki method di PaymentController dengan nama variabel yang tidak bentrok

    public function manualUpdateStatus($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            // Get status from Midtrans - gunakan nama variabel yang berbeda
            $midtransResponse = \Midtrans\Transaction::status($transaction->order_id);

            // Ensure $midtransResponse is an object
            if (is_array($midtransResponse)) {
                $midtransResponse = (object) $midtransResponse;
            }

            Log::info('Manual status update', [
                'order_id' => $transaction->order_id,
                'midtrans_transaction_status' => $midtransResponse->transaction_status,
                'current_db_status' => $transaction->status
            ]);

            // Determine new status
            $newStatus = $this->determineTransactionStatus(
                $midtransResponse->transaction_status,
                $midtransResponse->payment_type ?? '',
                $midtransResponse->fraud_status ?? ''
            );

            // Update database
            $oldStatus = $transaction->status;
            $transaction->update([
                'status' => $newStatus,
                'midtrans_response' => json_decode(json_encode($midtransResponse), true),
                'payment_type' => $midtransResponse->payment_type ?? null
            ]);

            // Send email if paid
            $emailSent = false;
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                try {
                    $this->sendTicketEmail($transaction);
                    $emailSent = true;
                } catch (\Exception $e) {
                    Log::error('Email sending failed', ['error' => $e->getMessage()]);
                }
            }

            return response()->json([
                'success' => true,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'midtrans_response' => $midtransResponse,
                'email_sent' => $emailSent
            ]);
        } catch (\Exception $e) {
            Log::error('Manual update status error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    public function debugSpecificTransaction($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            $debugInfo = [
                'transaction_info' => [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'current_status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'expires_at' => $transaction->expires_at,
                    'amount' => $transaction->amount,
                    'name' => $transaction->name,
                    'email' => $transaction->email,
                    'phone' => $transaction->phone,
                    'snap_token' => $transaction->snap_token ? 'EXISTS' : 'NULL',
                    'payment_type' => $transaction->payment_type,
                    'admin_fee' => $transaction->admin_fee
                ],
                'midtrans_config' => [
                    'server_key' => config('midtrans.server_key') ? 'EXISTS (***' . substr(config('midtrans.server_key'), -4) . ')' : 'NOT SET',
                    'client_key' => config('midtrans.client_key') ? 'EXISTS' : 'NOT SET',
                    'is_production' => config('midtrans.is_production', false),
                ],
                'last_midtrans_response' => $transaction->midtrans_response
            ];

            // Check current status from Midtrans
            try {
                $currentMidtransStatus = \Midtrans\Transaction::status($transaction->order_id);

                $debugInfo['current_midtrans_status'] = [
                    'raw_response' => $currentMidtransStatus,
                    'transaction_status' => $currentMidtransStatus->transaction_status ?? 'unknown',
                    'payment_type' => $currentMidtransStatus->payment_type ?? 'unknown',
                    'fraud_status' => $currentMidtransStatus->fraud_status ?? 'none',
                    'gross_amount' => $currentMidtransStatus->gross_amount ?? 'unknown'
                ];

                // Determine what the status should be
                $shouldBeStatus = $this->determineTransactionStatus(
                    $currentMidtransStatus->transaction_status ?? '',
                    $currentMidtransStatus->payment_type ?? '',
                    $currentMidtransStatus->fraud_status ?? ''
                );

                $debugInfo['status_analysis'] = [
                    'current_db_status' => $transaction->status,
                    'should_be_status' => $shouldBeStatus,
                    'needs_update' => $shouldBeStatus !== $transaction->status,
                    'midtrans_says' => $currentMidtransStatus->transaction_status ?? 'unknown'
                ];
            } catch (\Exception $e) {
                $debugInfo['midtrans_check_error'] = $e->getMessage();
            }

            return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Method untuk force update transaksi spesifik
    public function forceUpdateSpecificTransaction($transactionId)
    {
        try {
            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            Log::info('=== FORCE UPDATE SPECIFIC TRANSACTION ===', [
                'transaction_id' => $transactionId,
                'order_id' => $transaction->order_id,
                'current_status' => $transaction->status
            ]);

            // Get current status from Midtrans
            $midtransResponse = \Midtrans\Transaction::status($transaction->order_id);

            if (is_array($midtransResponse)) {
                $midtransResponse = (object) $midtransResponse;
            }

            $newStatus = $this->determineTransactionStatus(
                $midtransResponse->transaction_status ?? '',
                $midtransResponse->payment_type ?? '',
                $midtransResponse->fraud_status ?? ''
            );

            $oldStatus = $transaction->status;

            // Force update
            $transaction->update([
                'status' => $newStatus,
                'midtrans_response' => json_decode(json_encode($midtransResponse), true),
                'payment_type' => $midtransResponse->payment_type ?? null,
                'updated_at' => now()
            ]);

            Log::info('=== FORCE UPDATE COMPLETED ===', [
                'transaction_id' => $transactionId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Send email if paid
            $emailSent = false;
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                try {
                    $this->sendTicketEmail($transaction);
                    $emailSent = true;
                    Log::info('=== EMAIL SENT ===', ['transaction_id' => $transactionId]);
                } catch (\Exception $e) {
                    Log::error('=== EMAIL FAILED ===', [
                        'transaction_id' => $transactionId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'midtrans_response' => $midtransResponse,
                'email_sent' => $emailSent
            ]);
        } catch (\Exception $e) {
            Log::error('=== FORCE UPDATE ERROR ===', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // method fix transaksi yang tertinggal bismillah
    public function fixStuckTransactions(Request $request)
    {
        try {
            Log::info('=== FIXING STUCK TRANSACTIONS ===');

            // Get all pending transactions that are potentially stuck
            $stuckTransactions = Transaction::where('status', 'pending')
                ->where('created_at', '>', Carbon::now()->subHours(72)) // Last 3 days
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Found stuck transactions', ['count' => $stuckTransactions->count()]);

            $results = [];
            $fixedCount = 0;

            foreach ($stuckTransactions as $transaction) {
                try {
                    Log::info('=== CHECKING STUCK TRANSACTION ===', [
                        'id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'created_at' => $transaction->created_at
                    ]);

                    // Setup Midtrans config
                    Config::$serverKey = config('midtrans.server_key');
                    Config::$isProduction = config('midtrans.is_production', false);

                    // Check status from Midtrans with retry mechanism
                    $retryCount = 0;
                    $maxRetries = 3;
                    $midtransResponse = null;

                    while ($retryCount < $maxRetries && !$midtransResponse) {
                        try {
                            $midtransResponse = \Midtrans\Transaction::status($transaction->order_id);
                            break;
                        } catch (\Exception $e) {
                            $retryCount++;
                            Log::warning('Midtrans API retry', [
                                'order_id' => $transaction->order_id,
                                'retry' => $retryCount,
                                'error' => $e->getMessage()
                            ]);

                            if ($retryCount < $maxRetries) {
                                sleep(2); // Wait 2 seconds before retry
                            }
                        }
                    }

                    if (!$midtransResponse) {
                        $results[] = [
                            'transaction_id' => $transaction->id,
                            'order_id' => $transaction->order_id,
                            'status' => 'api_error',
                            'message' => 'Failed to get status from Midtrans after retries'
                        ];
                        continue;
                    }

                    // Ensure response is object
                    if (is_array($midtransResponse)) {
                        $midtransResponse = (object) $midtransResponse;
                    }

                    Log::info('=== MIDTRANS RESPONSE FOR STUCK TRANSACTION ===', [
                        'order_id' => $transaction->order_id,
                        'transaction_status' => $midtransResponse->transaction_status ?? 'unknown',
                        'payment_type' => $midtransResponse->payment_type ?? 'unknown'
                    ]);

                    // Determine new status
                    $newStatus = $this->determineTransactionStatus(
                        $midtransResponse->transaction_status ?? '',
                        $midtransResponse->payment_type ?? '',
                        $midtransResponse->fraud_status ?? ''
                    );

                    $oldStatus = $transaction->status;

                    if ($newStatus !== $oldStatus) {
                        // Update transaction
                        $transaction->update([
                            'status' => $newStatus,
                            'midtrans_response' => json_decode(json_encode($midtransResponse), true),
                            'payment_type' => $midtransResponse->payment_type ?? null,
                            'updated_at' => now()
                        ]);

                        Log::info('=== STUCK TRANSACTION FIXED ===', [
                            'transaction_id' => $transaction->id,
                            'order_id' => $transaction->order_id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus
                        ]);

                        // Send email if paid
                        $emailSent = false;
                        if ($newStatus === 'paid') {
                            try {
                                $this->sendTicketEmail($transaction);
                                $emailSent = true;
                                Log::info('=== EMAIL SENT FOR FIXED TRANSACTION ===', [
                                    'transaction_id' => $transaction->id
                                ]);
                            } catch (\Exception $e) {
                                Log::error('=== EMAIL FAILED FOR FIXED TRANSACTION ===', [
                                    'transaction_id' => $transaction->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }

                        $results[] = [
                            'transaction_id' => $transaction->id,
                            'order_id' => $transaction->order_id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'fixed' => true,
                            'email_sent' => $emailSent,
                            'midtrans_status' => $midtransResponse->transaction_status
                        ];

                        $fixedCount++;
                    } else {
                        $results[] = [
                            'transaction_id' => $transaction->id,
                            'order_id' => $transaction->order_id,
                            'status' => $oldStatus,
                            'fixed' => false,
                            'message' => 'Status already correct',
                            'midtrans_status' => $midtransResponse->transaction_status
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('=== ERROR FIXING STUCK TRANSACTION ===', [
                        'transaction_id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'error' => $e->getMessage()
                    ]);

                    $results[] = [
                        'transaction_id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            }

            Log::info('=== STUCK TRANSACTIONS FIX COMPLETED ===', [
                'total_checked' => $stuckTransactions->count(),
                'fixed_count' => $fixedCount
            ]);

            return response()->json([
                'success' => true,
                'total_checked' => $stuckTransactions->count(),
                'fixed_count' => $fixedCount,
                'results' => $results
            ], 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            Log::error('=== STUCK TRANSACTIONS FIX FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk fix transaksi berdasarkan time range
    public function fixTransactionsByTimeRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        try {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            Log::info('=== FIXING TRANSACTIONS BY TIME RANGE ===', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            $transactions = Transaction::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Found transactions in time range', ['count' => $transactions->count()]);

            $results = [];
            $fixedCount = 0;

            // Setup Midtrans config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production', false);

            foreach ($transactions as $transaction) {
                try {
                    // Get status from Midtrans
                    $midtransResponse = \Midtrans\Transaction::status($transaction->order_id);

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
                            'payment_type' => $midtransResponse->payment_type ?? null
                        ]);

                        // Send email if paid
                        if ($newStatus === 'paid') {
                            try {
                                $this->sendTicketEmail($transaction);
                            } catch (\Exception $e) {
                                Log::error('Email failed', ['transaction_id' => $transaction->id]);
                            }
                        }

                        $results[] = [
                            'transaction_id' => $transaction->id,
                            'order_id' => $transaction->order_id,
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'fixed' => true
                        ];

                        $fixedCount++;
                    }
                } catch (\Exception $e) {
                    $results[] = [
                        'transaction_id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'date_range' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ],
                'total_checked' => $transactions->count(),
                'fixed_count' => $fixedCount,
                'results' => $results
            ], 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}