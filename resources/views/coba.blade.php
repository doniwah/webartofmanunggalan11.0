<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Payment Gateway</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-container {
            display: flex;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .payment-form {
            flex: 1;
            padding: 40px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
        }

        .character-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .monster-character {
            width: 300px;
            height: 400px;
            position: relative;
            animation: float 3s ease-in-out infinite;
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

        .monster-body {
            width: 200px;
            height: 250px;
            background: linear-gradient(45deg, #8b5cf6, #a855f7);
            border-radius: 50px 50px 20px 20px;
            position: relative;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
        }

        .monster-eyes {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
        }

        .eye {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            position: relative;
            animation: blink 4s infinite;
        }

        .eye::after {
            content: '';
            width: 20px;
            height: 20px;
            background: black;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes blink {

            0%,
            95%,
            100% {
                transform: scaleY(1);
            }

            97% {
                transform: scaleY(0.1);
            }
        }

        .monster-screen {
            width: 120px;
            height: 80px;
            background: #10b981;
            border-radius: 10px;
            position: absolute;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border: 3px solid white;
        }

        .monster-arms {
            position: absolute;
            top: 120px;
        }

        .arm {
            width: 30px;
            height: 80px;
            background: linear-gradient(45deg, #8b5cf6, #a855f7);
            border-radius: 15px;
            position: absolute;
        }

        .arm.left {
            left: -35px;
            transform: rotate(-20deg);
            animation: wave-left 2s ease-in-out infinite;
        }

        .arm.right {
            right: -35px;
            transform: rotate(20deg);
            animation: wave-right 2s ease-in-out infinite;
        }

        @keyframes wave-left {

            0%,
            100% {
                transform: rotate(-20deg);
            }

            50% {
                transform: rotate(-40deg);
            }
        }

        @keyframes wave-right {

            0%,
            100% {
                transform: rotate(20deg);
            }

            50% {
                transform: rotate(40deg);
            }
        }

        .rocket-flames {
            position: absolute;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #ff6b35, #f7931e, #ffcc02);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: flicker 0.5s ease-in-out infinite alternate;
        }

        @keyframes flicker {
            0% {
                transform: translateX(-50%) scale(1);
            }

            100% {
                transform: translateX(-50%) scale(1.1);
            }
        }

        .order-summary {
            margin-bottom: 30px;
        }

        .order-summary h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #10b981;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-item.total {
            font-weight: bold;
            font-size: 18px;
            color: #10b981;
            border-bottom: 2px solid #10b981;
        }

        .payment-details h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #10b981;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #ccc;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            font-size: 16px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .pay-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .pay-button:hover {
            background: linear-gradient(45deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .pay-button:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(16, 185, 129, 0.3);
            border-left-color: #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .payment-container {
                flex-direction: column;
            }

            .character-section {
                min-height: 300px;
            }

            .monster-character {
                width: 200px;
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <div class="payment-form">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-item">
                    <span>Ticket price</span>
                    <span>2 × $55</span>
                </div>
                <div class="order-item">
                    <span>Service fee</span>
                    <span>2 × $65</span>
                </div>
                <div class="order-item">
                    <span>Tax</span>
                    <span>2 × $10</span>
                </div>
                <div class="order-item total">
                    <span>Total price</span>
                    <span>$260</span>
                </div>
            </div>

            <div class="payment-details">
                <h2>Payment Details</h2>
                <form id="paymentForm">
                    <div class="form-group">
                        <label for="cardNumber">Card number</label>
                        <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456"
                            maxlength="19">
                    </div>

                    <div class="form-group">
                        <label for="cardHolder">Card holder</label>
                        <input type="text" id="cardHolder" name="cardHolder" placeholder="John Doe">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry">MM/YY</label>
                            <input type="text" id="expiry" name="expiry" placeholder="12/25" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                        </div>
                    </div>

                    <button type="submit" class="pay-button" id="payButton">
                        Pay $260
                    </button>
                </form>

                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Processing payment...</p>
                </div>
            </div>
        </div>

        <div class="character-section">
            <div class="monster-character">
                <div class="monster-body">
                    <div class="monster-eyes">
                        <div class="eye"></div>
                        <div class="eye"></div>
                    </div>
                    <div class="monster-screen">💳</div>
                    <div class="monster-arms">
                        <div class="arm left"></div>
                        <div class="arm right"></div>
                    </div>
                </div>
                <div class="rocket-flames"></div>
            </div>
        </div>
    </div>

    <script>
        // Format card number input
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Only allow numbers for CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Handle form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const payButton = document.getElementById('payButton');
            const loading = document.getElementById('loading');

            // Show loading state
            payButton.disabled = true;
            payButton.textContent = 'Processing...';
            loading.style.display = 'block';

            // Simulate Midtrans API call
            setTimeout(() => {
                // Here you would integrate with Midtrans API
                // For demo purposes, we'll show success message
                alert('Payment processed successfully! (This is a demo)');

                // Reset button state
                payButton.disabled = false;
                payButton.textContent = 'Pay $260';
                loading.style.display = 'none';

                // In real implementation, redirect to success page
                // window.location.href = '/payment-success';
            }, 3000);
        });

        // Add some interactive effects
        document.querySelector('.monster-screen').addEventListener('click', function() {
            this.style.background = this.style.background === 'rgb(239, 68, 68)' ? '#10b981' : '#ef4444';
            this.textContent = this.textContent === '💳' ? '❤️' : '💳';
        });
    </script>
</body>

</html>
