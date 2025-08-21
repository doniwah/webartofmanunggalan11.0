<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran Tiket</title>
    <style>
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

        .payment-container {
            background: rgba(42, 42, 62, 0.9);
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            backdrop-filter: blur(10px);
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #333;
            z-index: 1;
        }

        .progress-steps::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            width: 66.66%;
            height: 2px;
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            z-index: 2;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #2a2a3e;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            position: relative;
            z-index: 3;
        }

        .step.completed {
            background: #4ade80;
        }

        .step.active {
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
        }

        .payment-header {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .payment-summary {
            margin-bottom: 30px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-pay {
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            color: white;
            border: none;
            padding: 16px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: transform 0.3s;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
        }
    </style>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>

<body>
    <div class="payment-container">
        <div class="progress-steps">
            <div class="step completed">
                <div class="step-icon">🎫</div>
                <span>Pemesanan</span>
            </div>
            <div class="step active">
                <div class="step-icon">💳</div>
                <span>Pembayaran</span>
            </div>
            <div class="step">
                <div class="step-icon">✓</div>
                <span>Selesai</span>
            </div>
        </div>

        <h1 class="payment-header">Pembayaran Tiket</h1>

        <div class="payment-summary">
            <div class="summary-item">
                <span>Tiket Reguler</span>
                <span></span>
            </div>
            <div class="summary-item">
                <span>Biaya Admin</span>
                <span>Rp. 2.500</span>
            </div>
            <div class="summary-item total">
                <span>Total Pembayaran</span>
                <span></span>
            </div>
        </div>

        <button id="pay-button" class="btn-pay">
            Bayar Sekarang
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Opsi 1: Ambil data dari localStorage (jika menggunakan localStorage)
            const orderData = localStorage.getItem('orderData');
            if (orderData) {
                const data = JSON.parse(orderData);
                updatePaymentSummary(data);
            }

            // Opsi 2: Data sudah tersedia dari server (Laravel blade)
            // Data quantity, basePrice, totalAmount sudah di-pass dari controller
        });

        function updatePaymentSummary(data) {
            // Update ringkasan pembayaran
            const ticketSummary = document.querySelector('.summary-item span:last-child');
            if (ticketSummary) {
                ticketSummary.textContent = `${data.quantity} x Rp. ${data.unitPrice.toLocaleString('id-ID')}`;
            }

            const totalAmount = document.querySelector('.total span:last-child');
            if (totalAmount) {
                totalAmount.textContent = `Rp. ${data.total.toLocaleString('id-ID')}`;
            }
        }

        // Handler untuk tombol pembayaran
        document.getElementById('pay-button').addEventListener('click', function() {
            // Pastikan snapToken tersedia dari server
            const snapToken = '{{ $snapToken ?? '' }}'; // Dari Laravel blade

            if (!snapToken) {
                alert('Token pembayaran tidak tersedia');
                return;
            }

            snap.pay(snapToken, {
                onSuccess: function(result) {
                    // Bersihkan localStorage setelah pembayaran berhasil
                    localStorage.removeItem('orderData');
                    window.location.href = '/payment/success?order_id=' + result.order_id;
                },
                onPending: function(result) {
                    window.location.href = '/payment/pending?order_id=' + result.order_id;
                },
                onError: function(result) {
                    window.location.href = '/payment/failed?order_id=' + result.order_id;
                }
            });
        });
    </script>
</body>

</html>
