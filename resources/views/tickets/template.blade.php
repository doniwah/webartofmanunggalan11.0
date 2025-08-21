<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket AOM11</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .ticket-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }

        .event-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .event-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .ticket-body {
            padding: 40px 30px 30px;
        }

        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-section {
            flex: 1;
        }

        .info-section h4 {
            color: #8b5cf6;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-section p {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #e2e8f0;
        }

        .qr-code {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #64748b;
        }

        .qr-instruction {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .ticket-details {
            border-top: 2px dashed #e2e8f0;
            padding-top: 20px;
            margin-top: 20px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .detail-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .detail-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }

        .ticket-footer {
            background: #1e293b;
            color: white;
            padding: 20px 30px;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .order-id {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .important-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .important-note h5 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .important-note p {
            color: #92400e;
            font-size: 12px;
            margin: 0;
            line-height: 1.5;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <!-- Ticket Header -->
        <div class="ticket-header">
            <div class="event-title">AOM11 EVENT</div>
            <div class="event-subtitle">Tiket Reguler - {{ $transaction->quantity }} Tiket</div>
        </div>

        <!-- Ticket Body -->
        <div class="ticket-body">
            <!-- Event Info -->
            <div class="ticket-info">
                <div class="info-section">
                    <h4>Tanggal Event</h4>
                    <p>{{ $eventDate ?? '15 September 2025' }}</p>

                    <h4>Waktu</h4>
                    <p>{{ $eventTime ?? '19:00 WIB' }}</p>
                </div>
                <div class="info-section">
                    <h4>Tempat</h4>
                    <p>{{ $venue ?? 'Gedung Kesenian Jakarta' }}</p>

                    <h4>Jumlah Tiket</h4>
                    <p>{{ $transaction->quantity }} Orang</p>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="qr-code">
                    QR CODE
                    <br>
                    <small>{{ substr($qrData, 0, 20) }}...</small>
                </div>
                <div class="qr-instruction">
                    Tunjukkan QR Code ini saat masuk venue
                </div>
            </div>

            <!-- Customer Details -->
            <div class="ticket-details">
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nama Pemesan</div>
                        <div class="detail-value">{{ $transaction->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $transaction->email }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">No. Telepon</div>
                        <div class="detail-value">{{ $transaction->phone }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Pembayaran</div>
                        <div class="detail-value">Rp. {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="important-note">
                <h5>Catatan Penting:</h5>
                <p>• Harap datang 30 menit sebelum acara dimulai</p>
                <p>• Tunjukkan e-ticket dan identitas diri saat masuk</p>
                <p>• Tiket tidak dapat dikembalikan atau ditukar</p>
                <p>• Dilarang membawa makanan dan minuman dari luar</p>
            </div>
        </div>

        <!-- Ticket Footer -->
        <div class="ticket-footer">
            <div class="footer-text">Order ID:</div>
            <div class="order-id">{{ $transaction->order_id }}</div>
            <div class="footer-text" style="margin-top: 10px;">
                Diterbitkan pada: {{ Carbon\Carbon::now()->format('d F Y, H:i') }} WIB
            </div>
        </div>
    </div>
</body>

</html>
