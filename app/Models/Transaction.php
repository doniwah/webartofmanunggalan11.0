<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'phone',
        'email',
        'quantity',
        'amount',
        'admin_fee',
        'ticket_id', // TAMBAHKAN INI
        'status',
        'snap_token',
        'payment_type',
        'midtrans_response',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'midtrans_response' => 'array'
    ];

    // Tambahkan relasi ke Ticket
    public function ticket()
    {
        return $this->belongsTo(\App\Models\Ticket::class, 'ticket_id', 'idTicket');
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }
}