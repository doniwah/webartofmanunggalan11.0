<!DOCTYPE html>
<html>

<head>
    <title>Tiket AOM11 Anda</title>
</head>

<body>
    <h2>Halo {{ $transaction->name }},</h2>
    <p>Terima kasih telah membeli tiket AOM11. Berikut detail pembelian Anda:</p>

    <ul>
        <li>Order ID: {{ $transaction->order_id }}</li>
        <li>Jumlah Tiket: {{ $transaction->quantity }}</li>
        <li>Total Pembayaran: Rp {{ number_format($transaction->amount, 0, ',', '.') }}</li>
    </ul>

    <p>Tiket Anda terlampir dalam email ini. Anda juga dapat mengunduh tiket melalui link berikut:</p>
    <a href="{{ route('ticket.download', $transaction->id) }}">Download Tiket</a>

    <p>Jika Anda memiliki pertanyaan, silakan hubungi kami.</p>

    <p>Salam,<br>Tim AOM11</p>
</body>

</html>
