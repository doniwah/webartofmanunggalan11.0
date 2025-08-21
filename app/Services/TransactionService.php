<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function gettransactionbysales()
    {
        $web_transaction = Transaction::select(
            DB::raw("CASE WHEN ticket.name != ticket.sales_in THEN CONCAT(ticket.name, ' ', ticket.sales_in) ELSE ticket.name END AS name_ticket"),
            DB::raw("COUNT(transactions.id) AS transaction_count"),
            DB::raw("MAX(transactions.created_at) AS last_transaction")
        )
            ->join('ticket', 'ticket.idTicket', '=', 'transactions.ticket_id')
            ->where('transactions.status', 'pending') // atau status yang sesuai dengan confirmation = 2
            ->groupBy('transactions.ticket_id', 'ticket.name', 'ticket.sales_in')
            ->having('transaction_count', '!=', 0)
            ->orderBy('ticket.idTicket', 'asc')
            ->get();

        return $web_transaction;
    }

    // Alternative method jika ada masalah dengan join atau ingin lebih eksplisit
    public function gettransactionbysalesAlternative()
    {
        $web_transaction = DB::table('transactions')
            ->select(
                DB::raw("CASE WHEN ticket.name != ticket.sales_in THEN CONCAT(ticket.name, ' ', ticket.sales_in) ELSE ticket.name END AS name_ticket"),
                DB::raw("COUNT(transactions.id) AS transaction_count"),
                DB::raw("MAX(transactions.created_at) AS last_transaction")
            )
            ->join('ticket', 'ticket.idTicket', '=', 'transactions.ticket_id')
            ->where('transactions.status', 'pending')
            ->groupBy('transactions.ticket_id', 'ticket.name', 'ticket.sales_in')
            ->having('transaction_count', '>', 0)
            ->orderBy('ticket.idTicket', 'asc')
            ->get();

        return $web_transaction;
    }

    // Method untuk get semua transactions
    public function getAllTransactions()
    {
        return Transaction::with(['ticket'])->get();
    }

    // Method untuk get transaction by status
    public function getTransactionsByStatus($status)
    {
        return Transaction::where('status', $status)->get();
    }

    // Method untuk get transaction count by ticket
    public function getTransactionCountByTicket()
    {
        return Transaction::select('ticket_id', DB::raw('COUNT(*) as count'))
            ->groupBy('ticket_id')
            ->get();
    }

    // Method gettransaction yang hilang - return query builder bukan collection
    public function gettransaction()
    {
        return Transaction::with(['ticket'])
            ->orderBy('created_at', 'desc');
    }

    // Method untuk get all transactions sebagai collection
    public function getAllTransactionsCollection()
    {
        return Transaction::with(['ticket'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Method untuk get recent transactions
    public function getRecentTransactions($limit = 10)
    {
        return Transaction::with(['ticket'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Method untuk get transactions with specific status
    public function getTransactionsByStatusWithTicket($status = null)
    {
        $query = Transaction::with(['ticket']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    // Method untuk dashboard stats
    public function getDashboardStats()
    {
        return [
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'completed_transactions' => Transaction::where('status', 'settlement')->count(),
            'total_revenue' => Transaction::where('status', 'settlement')->sum('amount'),
            'recent_transactions' => $this->getRecentTransactions(5)
        ];
    }

    // Method khusus untuk dashboard - get paid transactions count
    public function getPaidTransactionsCount()
    {
        try {
            // Sesuaikan dengan status yang ada: 'settlement' untuk transaksi berhasil
            return Transaction::whereIn('status', ['paid', 'settlement', 'capture'])->count();
        } catch (\Exception $e) {
            Log::error('Error getting paid transactions count: ' . $e->getMessage());
            return 0;
        }
    }

    // Method khusus untuk dashboard - get today's transactions grouped by ticket
    public function getTodayTransactionsByTicket()
    {
        try {
            $transactions = Transaction::with(['ticket'])
                ->whereDate('created_at', today())
                ->whereIn('status', ['settlement', 'paid', 'capture']) // hanya yang berhasil
                ->get();

            if ($transactions->isEmpty()) {
                return collect([]);
            }

            return $transactions->groupBy('ticket_id')
                ->map(function ($group) {
                    $ticket = $group->first()->ticket;
                    return [
                        'name' => $ticket ? $ticket->name : 'Unknown Ticket',
                        'jumlah' => $group->count(),
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error getting today transactions by ticket: ' . $e->getMessage());
            return collect([]);
        }
    }

    // Method untuk mendapatkan data untuk variabel $ticketdiambils di view
    public function getTicketsAttendedCount()
    {
        try {
            // Asumsi: ada kolom 'attendance_status' atau cara lain menandai kehadiran
            // Jika tidak ada, gunakan status transaksi yang berhasil sebagai proxy
            return Transaction::whereIn('status', ['settlement', 'paid', 'capture'])
                ->sum('quantity'); // total quantity tiket yang terjual
        } catch (\Exception $e) {
            Log::error('Error getting tickets attended count: ' . $e->getMessage());
            return 0;
        }
    }

    // Method untuk mendapatkan data penjualan ticket (untuk table di view)
    public function getPenjualanTicket()
    {
        try {
            return $this->gettransactionbysales();
        } catch (\Exception $e) {
            Log::error('Error getting penjualan ticket: ' . $e->getMessage());
            return collect([]);
        }
    }

    // Method debug untuk cek koneksi database
    public function debugDatabase()
    {
        try {
            $totalTransactions = Transaction::count();
            $todayTransactions = Transaction::whereDate('created_at', today())->count();

            return [
                'database_connected' => true,
                'total_transactions' => $totalTransactions,
                'today_transactions' => $todayTransactions,
                'sample_transaction' => Transaction::first()
            ];
        } catch (\Exception $e) {
            return [
                'database_connected' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // Method untuk get transactions by date range
    public function getTransactionsByDateRange($startDate, $endDate)
    {
        return Transaction::with(['ticket'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Method untuk get today's transactions
    public function getTodayTransactions()
    {
        return Transaction::with(['ticket'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Method untuk get transaction summary by date
    public function getTransactionSummaryByDate($startDate = null, $endDate = null)
    {
        $query = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(CASE WHEN status = "settlement" THEN amount ELSE 0 END) as total_revenue'),
            DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count'),
            DB::raw('COUNT(CASE WHEN status = "settlement" THEN 1 END) as completed_count')
        );

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();
    }
}