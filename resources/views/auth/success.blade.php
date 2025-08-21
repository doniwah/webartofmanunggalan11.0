<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: url("/images/background_login.png");
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .success-container {
            background: rgba(42, 42, 62, 0.9);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 12px;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .success-message {
            font-size: 16px;
            color: #9ca3af;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .transaction-details {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
            font-weight: bold;
            font-size: 18px;
        }

        .detail-label {
            color: #9ca3af;
        }

        .detail-value {
            color: white;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 15px;
            flex-direction: column;
        }

        .btn {
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #8b5cf6;
            border: 2px solid #8b5cf6;
        }

        .btn-secondary:hover {
            background: rgba(139, 92, 246, 0.1);
        }

        .order-id {
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .success-container {
                padding: 30px 20px;
            }

            .success-title {
                font-size: 24px;
            }

            .actions {
                gap: 12px;
            }

            .btn {
                padding: 14px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-icon">✓</div>

        <h1 class="success-title">Pembayaran Berhasil!</h1>

        <p class="success-message">
            Terima kasih! Pembayaran Anda telah berhasil diproses.
            Tiket elektronik akan dikirim ke email Anda dalam beberapa menit.
        </p>

        <div class="transaction-details">
            <div class="detail-item">
                <span class="detail-label">Order ID</span>
                <span class="detail-value order-id">{{ $transaction->order_id ?? 'TKT-XXXX' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Nama</span>
                <span class="detail-value">{{ $transaction->name ?? 'Customer' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ $transaction->email ?? 'customer@email.com' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Jumlah Tiket</span>
                <span class="detail-value">{{ $transaction->quantity ?? 1 }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Total Pembayaran</span>
                <span class="detail-value">Rp. {{ number_format($transaction->amount ?? 52500, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="actions">
            <button onclick="downloadTicket()" class="btn btn-primary">
                Download E-Ticket
            </button>
            <a href="/payment-silver" class="btn btn-secondary">
                Beli Tiket Lagi
            </a>
        </div>
    </div>

    <script>
        function downloadTicket() {
            // Implement ticket download functionality
            alert('Fitur download e-ticket akan segera tersedia. E-ticket akan dikirim ke email Anda.');

            // Example implementation:
            // window.open('/download-ticket/{{ $transaction->id ?? 1 }}', '_blank');
        }

        // Auto-redirect after 30 seconds
        setTimeout(() => {
            if (confirm('Halaman akan dialihkan ke beranda. Lanjutkan?')) {
                window.location.href = '/';
            }
        }, 3000);
    </script>
    <script>
        // Bersihkan localStorage saat tombol "Beli Tiket Lagi" diklik
        document.querySelector('.btn-secondary').addEventListener('click', function(e) {
            // Hapus semua data transaksi
            localStorage.removeItem('currentTransaction');

            // Tambahkan parameter ke URL untuk membersihkan session
            this.href = '/payment-silver?clean=1';
        });

        // Bersihkan localStorage saat halaman success dimuat
        window.addEventListener('DOMContentLoaded', function() {
            localStorage.removeItem('currentTransaction');
        });
        document.getElementById('cancelPaymentBtn').onclick = function() {
            // Hapus semua data transaksi
            clearTransactionState();

            // Redirect ke halaman checkout dengan parameter clean
            window.location.href = '/payment-silver?clean=1';
        };
    </script>
</body>

</html>
