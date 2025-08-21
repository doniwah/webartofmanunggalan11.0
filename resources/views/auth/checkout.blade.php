<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/images/aom_maskot.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembelian Tiket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: url("/images/background_login.png") no-repeat center center;
            background-color: black;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            gap: 60px;
            align-items: center;
        }

        .left-section {
            flex: 1;
            margin-left: 100px;
            margin-top: 150px !important;
            max-width: 500px;
        }

        .right-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
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
            width: 33.33%;
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

        .step.active {
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
        }

        .step.completed {
            background: #4ade80;
        }

        .step-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .step.active .step-icon {
            background: rgba(255, 255, 255, 0.9);
            color: #8b5cf6;
        }

        /* Price Section */
        .price {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 8px;
            background: linear-gradient(90deg, #8b5cf6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .ticket-type {
            font-size: 18px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .price-note {
            color: #9ca3af;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .warning {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 12px 16px;
            margin-bottom: 30px;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Quantity Section */
        .quantity-section {
            margin-bottom: 30px;
        }

        .quantity-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #8b5cf6;
            background: transparent;
            color: #8b5cf6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 20px;
            font-weight: bold;
        }

        .quantity-btn:hover {
            background: #8b5cf6;
            color: white;
            transform: scale(1.05);
        }

        .quantity-display {
            font-size: 24px;
            font-weight: bold;
            min-width: 40px;
            text-align: center;
        }

        /* Confirm Button */
        .confirm-btn {
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
            margin-bottom: 30px;
        }

        .confirm-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        /* Benefits Section */
        .benefits {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .benefits-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .benefits-text {
            color: #9ca3af;
            font-size: 14px;
        }

        /* Form Section */
        .form-section {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #e5e7eb;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }

        .form-group input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .error-message {
            display: none;
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .error-message.show {
            display: block;
        }

        .confirm-btn.disabled {
            background: #4b5563;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .confirm-btn.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Mascot Character */
        .mascot {
            position: relative;
            animation: float 3s ease-in-out infinite;
        }

        .mascot-placeholder img {
            width: 100%;
            height: 100%;
            background: transparent;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            position: relative;
            overflow: hidden;
            scale: 0.6;
            top: 20px;
        }



        /* Payment Gateway Styles */
        .payment-section {
            display: none;
            width: 100%;
            margin-top: -100px;
        }
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .payment-method {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #8b5cf6;
            background: rgba(255, 255, 255, 0.1);
        }

        .payment-method.active {
            border-color: #8b5cf6;
            background: rgba(139, 92, 246, 0.2);
        }

        .payment-method img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .payment-method .method-name {
            font-weight: 600;
        }

        .payment-summary {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-item.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-weight: bold;
            font-size: 18px;
        }

        .pay-now-btn {
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
            margin-bottom: 20px;
        }

        .pay-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .pay-now-btn:disabled {
            background: #4b5563;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pay-now-btn:disabled:hover {
            transform: none;
            box-shadow: none;
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
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: rgba(139, 92, 246, 0.1);
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Alert Messages */
        .alert {
        position: relative;
        z-index: 9999;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 15px; /* kasih jarak ke bawah */
        font-size: 14px;
        top: -100px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-left: 4px solid #22c55e;
            color: #86efac;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-left: 4px solid #f59e0b;
            color: #fcd34d;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: rgba(0, 0, 0, 0.8) !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            z-index: 9999 !important;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .modal.show {
            opacity: 1 !important;
            pointer-events: all !important;
        }

        .modal-content {
            background: #2a2a3e !important;
            padding: 30px !important;
            border-radius: 16px !important;
            width: 90% !important;
            max-width: 500px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            position: relative !important;
            z-index: 10000 !important;
        }

        .modal.show .modal-content {
            transform: translateY(0) !important;
        }

        .modal h3 {
            font-size: 24px !important;
            margin-bottom: 15px !important;
            color: #a855f7 !important;
        }

        .modal p {
            font-size: 16px !important;
            margin-bottom: 25px !important;
            line-height: 1.5 !important;
            color: #e5e7eb !important;
        }

        .modal-buttons {
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
        }

        .modal-buttons button {
            width: 100% !important;
            padding: 14px 20px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        #continuePaymentBtn {
            background: linear-gradient(90deg, #8b5cf6, #a855f7) !important;
            border: none !important;
            color: white !important;
        }

        #continuePaymentBtn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4) !important;
        }

        #cancelPaymentBtn {
            background: transparent !important;
            border: 2px solid #8b5cf6 !important;
            color: #8b5cf6 !important;
        }

        #cancelPaymentBtn:hover {
            background: rgba(139, 92, 246, 0.1) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                gap: 30px;
            }

            .progress-steps {
                flex-wrap: wrap;
                gap: 10px;
            }

            .price {
                font-size: 36px;
            }

            .mascot-placeholder {
                width: 300px;
                height: 350px;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }

            .left-section {
                margin-left: 0px;
                margin-top: 150px !important;
            }

            .mascot-placeholder {
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden; /* jaga-jaga kalau ada isi lain */
            }
        }
    </style>
    <!-- Midtrans Snap JS -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-UqiJMUuzKY5eiovw"></script>
</head>

<body>
    <div class="container">
        <div class="left-section">
            <!-- Alert Messages -->
            <div id="alert-container"></div>

            <!-- Ticket Order Section (Initially visible) -->
            <div id="ticket-order-section">
                <div class="price">Rp {{ number_format($selectedTicket->price ?? 50000, 0, ',', '.') }}</div>
                <div class="ticket-type">{{ $selectedTicket->name ?? 'Tiket Reguler' }}</div>
                <div class="price-note">Harga Anda hanya dijamin untuk kali ini!</div>

                <!-- Warning -->
                <div class="warning">
                    Pastikan jumlah tiket sudah benar sebelum melanjutkan.
                </div>

                <!-- Quantity Section -->
                <div class="quantity-section">
                    <div class="quantity-label">
                        <span>Jumlah Tiket</span>
                        <div class="quantity-controls">
                            <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                            <span class="quantity-display" id="quantity">1</span>
                            <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="form-section">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap *</label>
                        <input type="text" id="nama" placeholder="Masukkan nama lengkap Anda" required>
                        <span class="error-message" id="nama-error">Nama lengkap harus diisi</span>
                    </div>
                    <div class="form-group">
                        <label for="nomor">Nomor Telepon *</label>
                        <input type="tel" id="nomor" placeholder="Contoh: 089603159562" required>
                        <span class="error-message" id="nomor-error">Nomor telepon harus diisi</span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" placeholder="contoh@email.com" required>
                        <span class="error-message" id="email-error">Email harus diisi</span>
                    </div>
                </div>

                <!-- Confirm Button -->
                <button class="confirm-btn" onclick="confirmOrder()">
                    <span class="loading-spinner" id="confirm-spinner"></span>
                    <span id="confirm-text">Konfirmasi</span>
                </button>
            </div>

            <!-- Payment Section (Initially hidden) -->
            <div id="payment-section" class="payment-section">
                <div class="payment-summary">
                    <h3 style="margin-bottom: 15px;">Ringkasan Pembayaran</h3>
                    <div class="summary-item">
                        <span>{{ $selectedTicket->name ?? 'Tiket Reguler' }}</span>
                        <span id="summary-quantity">1 x Rp. 50.000</span>
                    </div>
                    <div class="summary-item">
                        <span>Biaya Admin</span>
                        <span>Rp. 2.500</span>
                    </div>
                    <div class="summary-item total">
                        <span>Total Pembayaran</span>
                        <span id="total-payment">Rp. 52.500</span>
                    </div>
                </div>

                <button class="pay-now-btn" onclick="payWithMidtrans()" id="pay-button">
                    <span class="loading-spinner" id="pay-spinner"></span>
                    <span id="pay-text">Bayar Sekarang</span>
                </button>
                <button class="back-btn" onclick="backToOrder()">Kembali</button>
            </div>
        </div>

        <div class="right-section">
            <div class="mascot">
                <div class="mascot-placeholder">
                    <img src="/images/maskot_aom11.png" alt="Mascot">
                </div>
            </div>
        </div>
    </div>

    <div id="pendingTransactionModal" class="modal" style="display: none; z-index: 9999;">
        <div class="modal-content">
            <h3>Pembayaran Tertunda</h3>
            <p id="pendingTransactionMessage">Anda memiliki pembayaran yang belum selesai.</p>
            <div class="modal-buttons">
                <button id="continuePaymentBtn" class="pay-now-btn" style="margin-bottom: 10px;">Lanjutkan
                    Pembayaran</button>
                <button id="cancelPaymentBtn" class="back-btn">Buat Pesanan Baru</button>
            </div>
        </div>
    </div>

    <script>
//     // DEBUG
//     console.log("=== DEBUGGING INFO ===");
// console.log("Selected Ticket dari server:", {
//     id: {{ $selectedTicket->idTicket ?? 'NULL' }},
//     name: "{{ $selectedTicket->name ?? 'NULL' }}",
//     price: {{ $selectedTicket->price ?? 'NULL' }},
//     quantity: {{ $selectedTicket->quantity ?? 'NULL' }}
// });
// console.log("URL saat ini:", window.location.href);
// console.log("URL Parameter:", new URLSearchParams(window.location.search).get('ticket_id'));


// function testTicketAPI() {
//     const ticketId = {{ $selectedTicket->idTicket ?? 0 }};
//     console.log("Testing dengan ticket_id:", ticketId);
    
//     fetch('/create-payment', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({
//             name: "Test User",
//             phone: "081234567890",
//             email: "test@example.com",
//             amount: 52500,
//             quantity: 1,
//             ticket_id: ticketId
//         })
//     })
//     .then(response => response.json())
//     .then(data => {
//         console.log("=== TEST API RESPONSE ===", data);
//         if (data.status === 'error') {
//             console.error("API Error:", data.message);
//         } else {
//             console.log("API Success:", data.message);
//         }
//     })
//     .catch(error => {
//         console.error("=== TEST API ERROR ===", error);
//     });
// }
//     // END DEBUG



        // Global Variables - Mock data untuk testing
        const selectedTicket = {
        id: {{ $selectedTicket->idTicket ?? 0 }},  // Gunakan idTicket bukan id
        name: '{{ $selectedTicket->name ?? "Tiket Reguler" }}',
        price: {{ $selectedTicket->price ?? 0 }}
        };

        const ticketId = {{ $selectedTicket->idTicket ?? 0 }};
        const basePrice = selectedTicket.price;
        const ticketName = selectedTicket.name;

        console.log("DEBUGGING - Selected Ticket Info:", {
        id: selectedTicket.id,
        name: selectedTicket.name,
        price: selectedTicket.price,
        ticketId: ticketId
        });

        let currentQuantity = 1;
        let selectedPaymentMethod = 'gopay';
        let transactionToken = '';
        let currentTransaction = null;

        console.log("Tiket dipilih:", ticketName, "Harga:", basePrice);

        // Core Functions
        function increaseQuantity() {
            if (currentQuantity < 10) {
                currentQuantity++;
                document.getElementById('quantity').textContent = currentQuantity;
                updateTicketDisplay();
            }
        }

        function decreaseQuantity() {
            if (currentQuantity > 1) {
                currentQuantity--;
                document.getElementById('quantity').textContent = currentQuantity;
                updateTicketDisplay();
            }
        }

        // FIXED CONFIRM ORDER FUNCTION
        function confirmOrder() {
            console.log('Confirm order clicked');

            // Validasi form
            if (!validateForm()) {
                showAlert('Mohon lengkapi semua data yang diperlukan', 'error');
                return;
            }

            // Show loading
            showLoading('confirm-spinner', 'confirm-text', 'Memproses...');

            // Simulate processing delay
            setTimeout(() => {
                hideLoading('confirm-spinner', 'confirm-text', 'Konfirmasi');

                // Show payment section
                document.getElementById('ticket-order-section').style.display = 'none';
                document.getElementById('payment-section').style.display = 'block';

                updateTicketDisplay();
                showAlert('Data berhasil dikonfirmasi! Silakan lanjutkan pembayaran.', 'success');
            }, 1000);
        }

        function backToOrder() {
            document.getElementById('ticket-order-section').style.display = 'block';
            document.getElementById('payment-section').style.display = 'none';
        }

        // Utility Functions
        function updateTicketDisplay() {
            const totalPrice = basePrice * currentQuantity;
            document.querySelector('.price').textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
            document.getElementById('summary-quantity').textContent =
                `${currentQuantity} x Rp ${basePrice.toLocaleString('id-ID')}`;

            const adminFee = 2500;
            const totalPayment = (basePrice * currentQuantity) + adminFee;
            document.getElementById('total-payment').textContent =
                `Rp ${totalPayment.toLocaleString('id-ID')}`;
        }

        function validateForm() {
            const nama = document.getElementById('nama').value.trim();
            const nomor = document.getElementById('nomor').value.trim();
            const email = document.getElementById('email').value.trim();

            let isValid = true;

            // Reset error states
            ['nama', 'nomor', 'email'].forEach(field => {
                const input = document.getElementById(field);
                const error = document.getElementById(field + '-error');
                if (input && error) {
                    input.classList.remove('error');
                    error.classList.remove('show');
                }
            });

            // Validate nama
            if (!nama) {
                showFieldError('nama', 'Nama lengkap harus diisi');
                isValid = false;
            } else if (nama.length < 3) {
                showFieldError('nama', 'Nama minimal 3 karakter');
                isValid = false;
            }

            // Validate nomor
            if (!nomor) {
                showFieldError('nomor', 'Nomor telepon harus diisi');
                isValid = false;
            } else if (!/^[0-9+\-\s()]+$/.test(nomor) || nomor.length < 10) {
                showFieldError('nomor', 'Nomor telepon tidak valid (minimal 10 digit)');
                isValid = false;
            }

            // Validate email
            if (!email) {
                showFieldError('email', 'Email harus diisi');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showFieldError('email', 'Format email tidak valid');
                isValid = false;
            }

            // Update button state
            const confirmBtn = document.querySelector('.confirm-btn');
            if (confirmBtn) {
                if (isValid) {
                    confirmBtn.classList.remove('disabled');
                } else {
                    confirmBtn.classList.add('disabled');
                }
            }

            return isValid;
        }

        function showFieldError(fieldName, message) {
            const input = document.getElementById(fieldName);
            const error = document.getElementById(fieldName + '-error');

            if (input && error) {
                input.classList.add('error');
                error.textContent = message;
                error.classList.add('show');
            }
        }

        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alert-container');
            if (alertContainer) {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type}`;
                alert.textContent = message;

                alertContainer.innerHTML = '';
                alertContainer.appendChild(alert);

                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }
        }

        function showLoading(spinnerId, textId, loadingText) {
            const spinner = document.getElementById(spinnerId);
            const text = document.getElementById(textId);

            if (spinner) spinner.style.display = 'inline-block';
            if (text) text.textContent = loadingText;
        }

        function hideLoading(spinnerId, textId, originalText) {
            const spinner = document.getElementById(spinnerId);
            const text = document.getElementById(textId);

            if (spinner) spinner.style.display = 'none';
            if (text) text.textContent = originalText;
        }

        // FIXED PAYMENT FUNCTION WITH MIDTRANS
    function payWithMidtrans() {
    console.log('=== PAYMENT START ===');

    const nama = document.getElementById('nama').value.trim();
    const nomor = document.getElementById('nomor').value.trim();
    const email = document.getElementById('email').value.trim();
    const quantity = currentQuantity;
    
    const finalTicketId = selectedTicket.id || {{ $selectedTicket->idTicket ?? 0 }};
    const amount = (selectedTicket.price * quantity) + 2500;

    console.log('=== PAYMENT DATA DEBUG ===', {
        nama: nama,
        nomor: nomor,
        email: email,
        quantity: quantity,
        ticketId: finalTicketId,
        amount: amount
    });

    if (!nama || !nomor || !email) {
        showAlert('Mohon lengkapi semua data terlebih dahulu', 'error');
        return;
    }

    if (!finalTicketId || finalTicketId === 0) {
        showAlert('ID Tiket tidak valid. Silakan refresh halaman dan coba lagi.', 'error');
        return;
    }

    showLoading('pay-spinner', 'pay-text', 'Memproses...');

    // Make API call to create payment
    fetch('/create-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: nama,
                phone: nomor,
                email: email,
                amount: amount,
                quantity: quantity,
                ticket_id: finalTicketId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('=== SERVER RESPONSE ===', data);
            
            if (data.status === 'success') {
                // Store transaction ID untuk reference
                const transactionId = data.transaction_id;
                
                // Open Midtrans payment popup dengan callback yang diperbaiki
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');
                        
                        // PERBAIKAN: Tunggu sebentar untuk memastikan webhook terproses
                        showAlert('Pembayaran berhasil! Mengalihkan ke halaman sukses...', 'success');
                        
                        // Delay redirect untuk memberi waktu webhook terproses
                        setTimeout(() => {
                            // Manual check status sebelum redirect
                            checkTransactionStatusBeforeRedirect(transactionId);
                        }, 2000); // 2 detik delay
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');
                        showAlert('Pembayaran tertunda! Silakan selesaikan pembayaran Anda.', 'warning');
                        
                        // Bisa redirect ke halaman pending atau tetap di halaman ini
                        setTimeout(() => {
                            window.location.href = `/payment-success/${transactionId}?status=pending`;
                        }, 1500);
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');
                        showAlert('Pembayaran gagal! Silakan coba lagi.', 'error');
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');
                        showAlert('Popup pembayaran ditutup. Silakan coba lagi jika pembayaran belum selesai.', 'warning');
                    }
                });
            } else {
                hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');
                showAlert(data.message || 'Gagal membuat transaksi. Silakan coba lagi.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');
            showAlert('Terjadi kesalahan saat memproses pembayaran', 'error');
        });
}

function checkTransactionStatusBeforeRedirect(transactionId) {
    console.log('=== CHECKING TRANSACTION STATUS BEFORE REDIRECT ===', transactionId);
    
    fetch(`/check-transaction-status-id/${transactionId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('=== STATUS CHECK RESPONSE ===', data);
        
        if (data.status === 'paid' || data.payment_status === 'paid') {
            // Status sudah paid, redirect ke success
            window.location.href = `/payment-success/${transactionId}?verified=1`;
        } else if (data.status === 'pending') {
            // Masih pending, tapi user sudah bayar - redirect dengan parameter khusus
            window.location.href = `/payment-success/${transactionId}?status=processing`;
        } else {
            // Status lain, redirect ke halaman sesuai status
            window.location.href = `/payment-success/${transactionId}?status=${data.status}`;
        }
    })
    .catch(error => {
        console.error('Error checking status:', error);
        // Fallback: redirect tanpa verifikasi
        window.location.href = `/payment-success/${transactionId}?fallback=1`;
    });
}

// FUNGSI TAMBAHAN: Polling status untuk kasus edge case
function startStatusPolling(transactionId, maxAttempts = 10) {
    let attempts = 0;
    
    const pollInterval = setInterval(() => {
        attempts++;
        console.log(`=== POLLING ATTEMPT ${attempts} ===`);
        
        fetch(`/check-transaction-status-id/${transactionId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'paid' || data.payment_status === 'paid') {
                    clearInterval(pollInterval);
                    console.log('=== STATUS POLLING SUCCESS - PAID ===');
                    window.location.href = `/payment-success/${transactionId}?polled=1`;
                } else if (attempts >= maxAttempts) {
                    clearInterval(pollInterval);
                    console.log('=== STATUS POLLING TIMEOUT ===');
                    window.location.href = `/payment-success/${transactionId}?timeout=1`;
                }
            })
            .catch(error => {
                console.error('Polling error:', error);
                if (attempts >= maxAttempts) {
                    clearInterval(pollInterval);
                    window.location.href = `/payment-success/${transactionId}?error=1`;
                }
            });
    }, 3000); // Poll setiap 3 detik
}

        // Fallback simulation jika Midtrans belum dimuat
        function showMidtransSimulation() {
            showLoading('pay-spinner', 'pay-text', 'Memproses...');

            // Simulate Midtrans popup
            const userConfirm = confirm('SIMULASI MIDTRANS POPUP\n\nTotal: Rp ' +
                ((basePrice * currentQuantity) + 2500).toLocaleString('id-ID') +
                '\n\nPilih OK untuk sukses, Cancel untuk batal');

            hideLoading('pay-spinner', 'pay-text', 'Bayar Sekarang');

            if (userConfirm) {
                showAlert('Pembayaran berhasil! (Simulasi)', 'success');
                setTimeout(() => {
                    // Reset form setelah sukses
                    resetForm();
                }, 2000);
            } else {
                showAlert('Pembayaran dibatalkan.', 'warning');
            }
        }

        function resetForm() {
            // Reset quantity
            currentQuantity = 1;
            document.getElementById('quantity').textContent = '1';
            updateTicketDisplay();

            // Clear form inputs
            document.getElementById('nama').value = '';
            document.getElementById('nomor').value = '';
            document.getElementById('email').value = '';

            // Reset validation states
            ['nama', 'nomor', 'email'].forEach(field => {
                const input = document.getElementById(field);
                const error = document.getElementById(field + '-error');
                if (input && error) {
                    input.classList.remove('error');
                    error.classList.remove('show');
                }
            });

            // Show order section
            backToOrder();

            // Clear alerts after delay
            setTimeout(() => {
                const alertContainer = document.getElementById('alert-container');
                if (alertContainer) {
                    alertContainer.innerHTML = '';
                }
            }, 3000);
        }

        // Setup form validation
        function setupFormValidation() {
            const inputs = ['nama', 'nomor', 'email'];

            inputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('input', validateForm);
                    input.addEventListener('blur', validateForm);
                }
            });

            // Initial validation
            validateForm();
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded, initializing...');

            // Initialize display
            updateTicketDisplay();

            // Setup form validation
            setupFormValidation();

            // Add hover effects to quantity buttons
            const quantityBtns = document.querySelectorAll('.quantity-btn');
            quantityBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1)';
                });
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            console.log('Page initialized successfully');
        });

        // Prevent form submission on enter key and trigger confirm instead
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                // Check if we're in the order section
                const orderSection = document.getElementById('ticket-order-section');
                if (orderSection && orderSection.style.display !== 'none') {
                    confirmOrder();
                }
            }
        });

        // Debug function untuk testing
        function debugInfo() {
            console.log('=== DEBUG INFO ===');
            console.log('selectedTicket:', selectedTicket);
            console.log('basePrice:', basePrice);
            console.log('currentQuantity:', currentQuantity);
            console.log('Midtrans available:', typeof window.snap !== 'undefined');
        }
    </script>
</body>

</html>
