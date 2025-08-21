<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $pdf;

    public function __construct(Transaction $transaction, $pdf)
    {
        $this->transaction = $transaction;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Tiket AOM11 - ' . $this->transaction->order_id)
            ->view('emails.ticket')
            ->attachData($this->pdf->output(), 'Tiket-AOM11-' . $this->transaction->order_id . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}