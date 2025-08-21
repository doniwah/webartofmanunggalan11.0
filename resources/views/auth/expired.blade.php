<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Kedaluwarsa</title>
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

        .expired-container {
            background: rgba(42, 42, 62, 0.9);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .expired-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(90deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            animation: shake 0.5s ease-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .expired-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 12px;
            background: linear-gradient(90deg, #ef4444, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .expired-message {
            font-size: 16px;
            color: #9ca3af;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .info-box {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #fca5a5;
        }

        .info-text {
            font-size: 14px;
            color: #9ca3af;
            line-height: 1.5;
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

        .countdown {
            display: none;
            font-size: 14px;
            color: #fbbf24;
            margin-top: 20px;
            padding: 12px;
            background: rgba(245, 158, 11, 0.1);
            border-radius: 8px;
        }

        .tips {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .tips-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #93c5fd;
        }

        .tips-list {
            list-style: none;
            padding: 0;
        }

        .tips-list li {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .tips-list li:before {
            content: "💡";
            position: absolute;
            left: 0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .expired-container {
                padding: 30px 20px;
            }

            .expired-title {
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
    <div class="expired-container">
        <div class="expired-icon">⏰</div>

        <h1 class="expired-title">Waktu Pembayaran Habis</h1>

        <p class="expired-message">
            Maaf, waktu untuk menyelesaikan pembayaran telah habis.
            Transaksi Anda telah dibatalkan secara otomatis.
        </p>

        <div class="info-box">
            <div class="info-title">Apa yang terjadi?</div>
            <div class="info-text">
                Setiap transaksi memiliki batas waktu 24 jam untuk diselesaikan.
                Hal ini dilakukan untuk menjaga ketersediaan tiket dan mencegah pemesanan yang tidak serius.
            </div>
        </div>

        <div class="tips">
            <div class="tips-title">Tips untuk pemesanan berikutnya:</div>
            <ul class="tips-list">
                <li>Pastikan Anda siap melakukan pembayaran sebelum memesan</li>
                <li>Gunakan metode pembayaran yang familiar bagi Anda</li>
                <li>Simpan informasi pembayaran dengan aman</li>
                <li>Selesaikan pembayaran sesegera mungkin</li>
            </ul>
        </div>

        <div class="actions">
            <a href="/payment-silver" class="btn btn-primary">
                Pesan Tiket Baru
            </a>
            <a href="/" class="btn btn-secondary">
                Kembali ke Beranda
            </a>
        </div>

        <div id="countdown" class="countdown">
            Halaman akan dialihkan ke beranda dalam <span id="countdown-timer">10</span> detik...
        </div>
    </div>

    <script>
        // Show countdown after 5 seconds
        setTimeout(() => {
            document.getElementById('countdown').style.display = 'block';

            let seconds = 10;
            const timer = document.getElementById('countdown-timer');

            const interval = setInterval(() => {
                seconds--;
                timer.textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = '/';
                }
            }, 1000);
        }, 5000);

        // Track user interaction to prevent auto-redirect
        let userInteracted = false;

        document.addEventListener('click', () => {
            userInteracted = true;
        });

        document.addEventListener('keypress', () => {
            userInteracted = true;
        });
    </script>
</body>

</html>
