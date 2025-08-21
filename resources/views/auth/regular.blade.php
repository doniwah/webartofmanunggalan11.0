<!DOCTYPE html>
<html>

<head>
    <title>Tiket AOM11 - {{ $transaction->order_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .ticket {
            border: 2px dashed #8b5cf6;
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
            background: #f9f9f9;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .details {
            margin: 15px 0;
        }

        .detail-item {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">
            <h2>Tiket AOM11</h2>
            <p>No. Tiket: {{ $transaction->order_id }}</p>
        </div>

        <div class="qr-code">
            <!-- Generate QR Code dengan nomor tiket -->
            {!! QrCode::size(150)->generate($transaction->order_id) !!}
        </div>

        <div class="details">
            <div class="detail-item">
                <strong>Nama:</strong> {{ $transaction->name }}
            </div>
            <div class="detail-item">
                <strong>Jumlah Tiket:</strong> {{ $transaction->quantity }}
            </div>
            <div class="detail-item">
                <strong>Tanggal Event:</strong> 15 Oktober 2023
            </div>
            <div class="detail-item">
                <strong>Lokasi:</strong> Gedung Serba Guna, Universitas Indonesia
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <small>*Tunjukkan tiket ini saat registrasi di lokasi event</small>
        </div>
    </div>
</body>

</html>
