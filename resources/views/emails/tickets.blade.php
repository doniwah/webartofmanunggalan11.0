<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket AOM11</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 40px 30px;
        }

        .success-message {
            background: #ecfdf5;
            border-left: 4px solid #22c55e;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }

        .success-message h3 {
            color: #16a34a;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .success-message p {
            color: #15803d;
            margin: 0;
            font-size: 14px;
        }

        .transaction-details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px solid #e2e8f0;
        }

        .detail-label {
            color: #64748b;
        }

        .detail-value {
            color: #1e293b;
            font-weight: 600;
        }

        .attachment-info {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .attachment-info h4 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .attachment-info p {
            color: #92400e;
            margin: 5px 0;
            font-size: 14px;
        }

        .instructions {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .instructions h4 {
            color: #1d4ed8;
            margin: 0 0 15px 0;
            font-size: 16px;
        }

        .instructions ul {
            color: #1e40af;
            margin: 0;
            padding-left: 20px;
        }

        .instructions li {
            margin: 8px 0;
            font-size: 14px;
        }

        .footer {
            background: #1e293b;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .footer p {
            margin: 5px 0;
            opacity: 0.8;
            font-size: 14px;
        }

        .order-id {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>🎫 E-Ticket AOM11</h1>
            <p>Pembayaran Berhasil!</p>
        </div>

        <div class="content">
            <div class="success-message">
                <h3>✅ Pembayaran Berhasil Dikonfirmasi</h3>
                <p>Terima kasih {{ $transaction->name }}! Pembayaran Anda telah berhasil diproses dan e-ticket telah
                    dilampirkan pada email ini.</p>
            </div>

            <h3>Detail Transaksi</h3>
            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value">{{ $transaction->order_id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nama Pemesan</span>
                    <span class="detail-value">{{ $transaction->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $transaction->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">No. Telepon</span>
                    <span class="detail-value">{{ $transaction->phone }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jumlah Tiket</span>
                    <span class="detail-value">{{ $transaction->quantity }} Tiket</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Pembayaran</span>
                    <span class="detail-value">Rp. {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="attachment-info">
                <h4>📎 File Terlampir</h4>
                <p>• E-Ticket dalam format PDF telah dilampirkan</p>
                <p>• Simpan dan print tiket untuk masuk ke venue</p>
                <p>• Atau tunjukkan e-ticket digital dari ponsel Anda</p>
            </div>

            <div class="instructions">
                <h4>📋 Petunjuk Penting</h4>
                <ul>
                    <li><strong>Datang lebih awal:</strong> Harap tiba di venue 30 menit sebelum acara dimulai</li>
                    <li><strong>Bawa identitas:</strong> Tunjukkan e-ticket dan kartu identitas saat masuk</li>
                    <li><strong>Simpan tiket:</strong> E-ticket hanya berlaku untuk 1 kali masuk, jangan hilang</li>
                    <li><strong>Ketentuan venue:</strong> Dilarang membawa makanan/minuman dari luar</li>
                    <li><strong>Kontak bantuan:</strong> Hubungi kami jika ada kendala teknis</li>
                </ul>
            </div>

            <h3>Informasi Event</h3>
            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label">Nama Event</span>
                    <span class="detail-value">AOM11 Event</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">15 September 2025</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu</span>
                    <span class="detail-value">19:00 WIB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Venue</span>
                    <span class="detail-value">Gedung Kesenian Jakarta</span>
                </div>
            </div>

            <p style="margin-top: 30px; text-align: center;">
                <strong>Terima kasih telah mempercayai kami!</strong><br>
                Sampai jumpa di event! 🎉
            </p>
        </div>

        <div class="footer">
            <p><strong>AOM11 Event Management</strong></p>
            <div class="order-id">{{ $transaction->order_id }}</div>
            <p>Email dikirim pada: {{ Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</p>
            <p style="font-size: 12px; margin-top: 20px;">
                Jika Anda tidak melakukan pemesanan ini, segera hubungi customer service kami.
            </p>
        </div>
    </div>
</body>

</html>
