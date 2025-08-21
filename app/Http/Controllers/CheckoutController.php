<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap; // Add this line if using Midtrans Snap API

class CheckoutController extends Controller
{
    public function showPayment(Request $request)
    {
        $quantity = $request->quantity;
        $basePrice = $request->base_price;
        $totalAmount = $request->total_amount;

        // Generate Snap Token
        $params = [
            'transaction_details' => [
                'order_id' => uniqid('ORDER-'),
                'gross_amount' => $totalAmount,
            ],
            'customer_details' => [
                'first_name' => 'Customer Name',
                'email' => 'customer@example.com',
            ],
            'item_details' => [
                [
                    'id' => 'ticket-regular',
                    'price' => $basePrice,
                    'quantity' => $quantity,
                    'name' => 'Tiket Reguler'
                ],
                [
                    'id' => 'admin-fee',
                    'price' => 2500,
                    'quantity' => 1,
                    'name' => 'Biaya Admin'
                ]
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('payment', compact('quantity', 'basePrice', 'totalAmount', 'snapToken'));
    }
}