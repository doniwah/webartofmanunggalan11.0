{{-- resources/views/payment/show.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran - {{ $transaction->order_id }}</title>
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

        .payment-container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .payment-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .order-id {
            color: #9ca3af;
            font-size: 14px;
        }

        .payment-details {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            font-weight: bold;
            font-size: 18px;
        }

        .detail-label {
            color: #9ca3af;
        }

        .detail-value {
            font-weight: 600;
        }

        .countdown-container {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
            text-align: center;
        }

        .countdown-text {
            font-size: 14px;
            margin-bottom: 5px;
            color: #ef4444;
        }

        .countdown-timer {
            font-size: 24px;
            font-weight: bold;
            color: #ef4444;
            font-family: monospace;
        }

        .pay-btn {
            width: 100%;
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            border: none;
            color: white;
            padding: 16px 24px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .pay-btn:disabled {
            background: #6b7280;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .cancel-btn {
            width: 100%;
            background: transparent;
            border: 2px solid #ef4444;
            color: #ef4444;
            padding: 16px 24px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .cancel-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .back-btn {
            width: 100%;
            background: transparent;
            border: 2px solid #8b5cf6;
            color: #8b5cf6;
            padding: 16px 24px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: block;
        }

        .back-btn:hover {
            background: rgba(139, 92, 246, 0.1);
        }

        .expired-message {
            text-align: center;
            color: #ef4444;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>

<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1 class="payment-title">Selesaikan Pembayaran</h1>
            <p class="order-id">Order ID: {{ $transaction->order_id }}</p>
        </div>

        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">Nama:</span>
                <span class="detail-value">{{ $transaction->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nomor Telepon:</span>
                <span class="detail-value">{{ $transaction->phone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tiket Reguler:</span>
                <span class="detail-value">{{ $transaction->quantity }} x Rp
                    {{ number_format($basePrice, 0, ',', '.') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Biaya Admin:</span>
                <span class="detail-value">Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Pembayaran:</span>
                <span class="detail-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="countdown-container" id="countdown-container">
            <div class="countdown-text">Waktu pembayaran tersisa:</div>
            <div class="countdown-timer" id="countdown-timer">--:--:--</div>
        </div>

        <div id="payment-buttons">
            <button class="pay-btn" id="pay-btn" onclick="continuePayment()">Bayar Sekarang</button>
            <button class="cancel-btn" onclick="cancelPayment()">Batalkan Pembayaran</button>
        </div>

        <div id="expired-content" style="display: none;">
            <div class="expired-message">Waktu pembayaran telah habis</div>
        </div>

        <a href="{{ route('payment.silver') }}" class="back-btn">Kembali ke Halaman Utama</a>
    </div>

    <script>
        const expiresAt = new Date('{{ $expiresAt->toISOString() }}');
        const snapToken = '{{ $snapToken }}';
        const transactionId = {{ $transaction->id }};
        let countdownInterval;

        function updateCountdown() {
            const now = new Date();
            const timeLeft = expiresAt - now;

            if (timeLeft <= 0) {
                // Expired
                document.getElementById('countdown-timer').textContent = '00:00:00';
                document.getElementById('countdown-container').style.display = 'none';
                document.getElementById('payment-buttons').style.display = 'none';
                document.getElementById('expired-content').style.display = 'block';

                clearInterval(countdownInterval);
                return;
            }

            const hours = Math.floor(timeLeft / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            document.getElementById('countdown-timer').textContent =
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function startCountdown() {
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
        }

        function continuePayment() {
            if (!snapToken) {
                alert('Token pembayaran tidak valid');
                return;
            }

            snap.pay(snapToken, {
                onSuccess: function(result) {
                    alert('Pembayaran berhasil!');
                    window.location.href = `/payment-success/${transactionId}`;
                },
                onPending: function(result) {
                    alert('Pembayaran tertunda! Silakan selesaikan pembayaran Anda.');
                    console.log('Payment pending:', result);
                },
                onError: function(result) {
                    alert('Pembayaran gagal! Silakan coba lagi.');
                    console.log('Payment error:', result);
                },
                onClose: function() {
                    console.log('Payment popup closed');
                }
            });
        }

        function cancelPayment() {
            if (confirm('Apakah Anda yakin ingin membatalkan pembayaran ini?')) {
                // Optional: Call API to cancel transaction
                fetch(`/cancel-payment/${transactionId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Pembayaran telah dibatalkan');
                        window.location.href = '{{ route('payment.silver') }}';
                    })
                    .catch(error => {
                        console.error('Cancel error:', error);
                        window.location.href = '{{ route('payment.silver') }}';
                    });
            }
        }

        // Check transaction status periodically
        function checkTransactionStatus() {
            fetch(`/transaction/${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'found') {
                        if (data.transaction.status === 'paid') {
                            clearInterval(countdownInterval);
                            alert('Pembayaran telah berhasil diselesaikan!');
                            window.location.href = `/payment-success/${transactionId}`;
                        } else if (data.transaction.status === 'expired') {
                            clearInterval(countdownInterval);
                            document.getElementById('countdown-container').style.display = 'none';
                            document.getElementById('payment-buttons').style.display = 'none';
                            document.getElementById('expired-content').style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();

            // Check status every 30 seconds
            setInterval(checkTransactionStatus, 30000);
        });
    </script>
</body>

</html>
