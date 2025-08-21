<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionService $transactionService, UserService $userservice)
    {
        try {
            // Sesuai dengan variabel di view
            $ticketsdhdibayars = $transactionService->getPaidTransactionsCount();
            $transaksiperhari = $transactionService->getTodayTransactionsByTicket();
            $ticketdiambils = $transactionService->getTicketsAttendedCount();
            $penjualanticket = $transactionService->getPenjualanTicket();

            return view('pages.admin.dashboard', compact(
                'ticketsdhdibayars',
                'transaksiperhari',
                'ticketdiambils',
                'penjualanticket'
            ));
        } catch (\Exception $e) {
            // Log error dan return view dengan data kosong
            Log::error('Dashboard error: ' . $e->getMessage());

            return view('pages.admin.dashboard', [
                'ticketsdhdibayars' => 0,
                'transaksiperhari' => collect([]),
                'ticketdiambils' => 0,
                'penjualanticket' => collect([])
            ]);
        }
    }
}