<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran Sedang Diproses</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: url("/images/background_login.png");
            background-size: cover;
            background-position: center;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            text-align: center;
            max-width: 600px;
            background: rgba(42, 42, 62, 0.95);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .loading-animation {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(139, 92, 246, 0.3);
            border-top: 4px solid #8b5cf6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #e5e7eb;
            line-height: 1.6;
        }

        .transaction-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #9ca3af;
            font-weight: 500;
        }

        .info-value {
            color: #e5e7eb;
            font-weight: 600;
        }

        .status-pending {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #fbbf24;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .countdown {
            font-size: 16px;
            color: #fbbf24;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            padding: 14px 24px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: none;
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

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            color: #93c5fd;
        }

        .manual-check {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 14px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading-animation"></div>
        
        <h1 class="title">Pembayaran Sedang Diproses</h1>
        
        <div class="message">
            {{ $message ?? 'Kami sedang memverifikasi pembayaran Anda. Mohon tunggu sebentar...' }}
        </div>

        <div class="transaction-info">
            <div class="info-row">
                <span class="info-label">Order ID:</span>
                <span class="info-value">{{ $transaction->order_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama:</span>
                <span class="info-value">{{ $transaction->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total:</span>
                <span class="info-value">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-pending">
                        <span class="status-dot"></span>
                        Sedang Diproses
                    </span>
                </span>
            </div>
        </div>

        <div class="countdown" id="countdown">
            Halaman akan refresh dalam <span id="countdown-seconds">{{ $refresh_interval ?? 10 }}</span> detik
        </div>

        <div class="alert alert-info">
            <strong>Catatan:</strong> Jika pembayaran sudah berhasil dilakukan, status akan otomatis berubah dalam beberapa saat.
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="manualRefresh()">
                <span id="refresh-text">Refresh Status</span>
                <span id="refresh-loading" style="display: none;">⟳ Checking...</span>
            </button>
            
            <a href="{{ route('payment.silver', ['ticket_id' => $transaction->ticket_id]) }}" class="btn btn-secondary">
                Kembali ke Pembayaran
            </a>
        </div>

        <div class="manual-check">
            Jika status tidak berubah dalam 5 menit, silakan hubungi customer service atau coba refresh halaman ini.
        </div>
    </div>

    <script>
        let countdownSeconds = {{ $refresh_interval ?? 10 }};
        let countdownInterval;
        let refreshCheckInterval;
        
        // Countdown untuk auto refresh
        function startCountdown() {
            const countdownElement = document.getElementById('countdown-seconds');
            
            countdownInterval = setInterval(() => {
                countdownSeconds--;
                countdownElement.textContent = countdownSeconds;
                
                if (countdownSeconds <= 0) {
                    clearInterval(countdownInterval);
                    window.location.reload();
                }
            }, 1000);
        }
        
        // Auto check status setiap 5 detik
        function startStatusCheck() {
            refreshCheckInterval = setInterval(() => {
                checkTransactionStatus();
            }, 5000);
        }
        
        // Manual refresh
        function manualRefresh() {
            const refreshText = document.getElementById('refresh-text');
            const refreshLoading = document.getElementById('refresh-loading');
            
            refreshText.style.display = 'none';
            refreshLoading.style.display = 'inline';
            
            checkTransactionStatus(true);
        }
        
        // Check transaction status
        function checkTransactionStatus(isManual = false) {
            const transactionId = {{ $transaction->id }};
            
            fetch(`/refresh-transaction-status/${transactionId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Status check result:', data);
                
                if (data.status === 'success' && data.transaction.can_show_success) {
                    // Status sudah paid, redirect ke success
                    clearInterval(countdownInterval);
                    clearInterval(refreshCheckInterval);
                    window.location.href = `/payment-success/${transactionId}?verified=1`;
                } else if (isManual) {
                    // Reset manual refresh button
                    const refreshText = document.getElementById('refresh-text');
                    const refreshLoading = document.getElementById('refresh-loading');
                    
                    refreshText.style.display = 'inline';
                    refreshLoading.style.display = 'none';
                    
                    if (data.new_status === 'paid') {
                        window.location.href = `/payment-success/${transactionId}?verified=1`;
                    } else {
                        alert('Status masih: ' + (data.new_status || 'pending'));
                    }
                }
            })
            .catch(error => {
                console.error('Status check error:', error);
                
                if (isManual) {
                    const refreshText = document.getElementById('refresh-text');
                    const refreshLoading = document.getElementById('refresh-loading');
                    
                    refreshText.style.display = 'inline';
                    refreshLoading.style.display = 'none';
                }
            });
        }
        
        // Start when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
            startStatusCheck();
            
            // Check status immediately
            setTimeout(() => {
                checkTransactionStatus();
            }, 2000);
        });
        
        // Clean up intervals when leaving page
        window.addEventListener('beforeunload', function() {
            clearInterval(countdownInterval);
            clearInterval(refreshCheckInterval);
        });
    </script>
</body>
</html>