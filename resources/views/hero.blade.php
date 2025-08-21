<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AOM 11.0</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/animate.css">

    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: url('images/bg_aom11.png');
            color: white;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Stars background */
        /* body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(2px 2px at 20px 30px, #eee, transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.8), transparent),
                radial-gradient(1px 1px at 90px 40px, #fff, transparent),
                radial-gradient(1px 1px at 130px 80px, rgba(255, 255, 255, 0.6), transparent),
                radial-gradient(2px 2px at 160px 30px, #fff, transparent);
            background-repeat: repeat;
            background-size: 200px 100px;
            animation: twinkle 3s infinite;
            z-index: 1;
        } */

        @keyframes twinkle {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }

        .left-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .right-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        /* AOM 11.0 Title */
        .aom-title {
            width: 400px;
            height: 120px;
            background: url('/images/hero_aom11.png') no-repeat center;
            background-size: contain;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.5));
            scale: 1.9;
            margin-left: 100px;
        }


        /* Subtitle */
        .subtitle {
            width: 350px;
            height: 40px;
            background: url('/images/hero_teks.png') no-repeat center;
            background-size: contain;
            margin-bottom: 40px;
            margin-left: 100px;
        }

        /* Countdown */
        .countdown {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            margin-left: 100px;
        }

        .time-box {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 20px 15px;
            text-align: center;
            min-width: 80px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .digit {
            font-size: 36px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 5px;
        }

        .countdown-label {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Buy Ticket Button */
        .buy-ticket-btn {
            background: linear-gradient(135deg, #8B5CF6, #A855F7, #9333EA);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 100px;
        }

        .buy-ticket-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(139, 92, 246, 0.6);
            background: linear-gradient(135deg, #9333EA, #8B5CF6, #A855F7);
        }

        /* Arena Image */
        .arena-container {
            position: relative;
            width: 100%;
            max-width: 500px;
        }

        .arena-image {
            width: 100%;
            height: 350px;
            background: linear-gradient(135deg, #4C1D95, #5B21B6, #6D28D9);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            position: relative;
        }

        .arena-placeholder {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
            text-align: center;
            z-index: 2;
        }

        .arena-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 30% 30%, rgba(168, 85, 247, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(139, 92, 246, 0.3) 0%, transparent 50%);
            z-index: 1;
        }

        /* Dots indicator */
        .dots-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: white;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .countdown {
                gap: 15px;
            }

            .time-box {
                min-width: 70px;
                padding: 15px 10px;
            }

            .digit {
                font-size: 28px;
            }

            .aom-title {
                width: 300px;
                height: 90px;
            }

            .subtitle {
                width: 280px;
                height: 35px;
            }
        }

        /* START CSS GUESTAR */

        .guestar-body {
            /* background: linear-gradient(135deg, #0a0a0a, #1a1a1a, #2a2a2a); */
            color: white;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            overflow: hidden;
            position: relative;
        }

        /* Stars background */
        .guestar-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(2px 2px at 20px 30px, #fff, transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.8), transparent),
                radial-gradient(1px 1px at 90px 40px, #fff, transparent),
                radial-gradient(1px 1px at 130px 80px, rgba(255, 255, 255, 0.6), transparent),
                radial-gradient(2px 2px at 160px 30px, #fff, transparent);
            background-repeat: repeat;
            background-size: 200px 100px;
            animation: guestar-twinkle 3s infinite;
            z-index: 1;
        }

        @keyframes guestar-twinkle {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .guestar-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
        }

        /* Title */
        .guestar-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .guestar-title-image {
            width: 400px;
            height: 80px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 80"><defs><filter id="neon"><feGaussianBlur stdDeviation="3" result="coloredBlur"/><feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><text x="200" y="45" font-family="Arial Black" font-size="36" font-weight="900" text-anchor="middle" fill="white" stroke="rgba(255,255,255,0.8)" stroke-width="1" filter="url(%23neon)">GUESTSTAR</text></svg>') no-repeat center;
            background-size: contain;
            margin: 0 auto;
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8)) drop-shadow(0 0 40px rgba(255, 255, 255, 0.4));
        }

        /* Carousel */
        .guestar-carousel {
            position: center;
            overflow: hidden;
            border-radius: 20px;
        }

        .guestar-carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .guestar-slide {
            min-width: 25%;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .guestar-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            height: 400px;
        }

        .guestar-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .guestar-image {
            width: 100%;
            height: 300px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .guestar-image img {
            width: 100%;
            height: 100%;
        }

        .guestar-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        }

        /* Artist images */
        .guestar-ariana {
            background-image: linear-gradient(45deg, #ff6b6b, #ffa500, #ff1744);
        }

        .guestar-ed {
            background-image: linear-gradient(45deg, #4ecdc4, #44a08d, #2196f3);
        }

        .guestar-adele {
            background-image: linear-gradient(45deg, #667eea, #764ba2, #9c27b0);
        }

        .guestar-taylor {
            background-image: linear-gradient(45deg, #ffeaa7, #fab1a0, #e17055);
        }

        .guestar-info {
            padding: 20px;
            text-align: center;
        }

        .guestar-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            color: white;
        }

        .guestar-label {
            font-size: 14px;
            color: #b19cd9;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Navigation arrows */
        .guestar-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .guestar-nav:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-50%) scale(1.1);
        }

        .guestar-prev {
            left: -25px;
        }

        .guestar-next {
            right: -25px;
        }

        .guestar-nav::before {
            content: '';
            width: 12px;
            height: 12px;
            border-top: 2px solid white;
            border-right: 2px solid white;
        }

        .guestar-prev::before {
            transform: rotate(-135deg);
            margin-left: 3px;
        }

        .guestar-next::before {
            transform: rotate(45deg);
            margin-right: 3px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .guestar-slide {
                min-width: 50%;
            }

            .guestar-title-image {
                width: 300px;
                height: 60px;
            }

            .guestar-card {
                height: 350px;
            }

            .guestar-image {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .guestar-slide {
                min-width: 100%;
            }

            .guestar-nav {
                width: 40px;
                height: 40px;
            }
        }

        /* Smooth animations */
        .guestar-card {
            animation: guestar-fadeIn 0.6s ease-out;
        }

        @keyframes guestar-fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* START CSS TICKET */
        .ticket-body {
            /* background: linear-gradient(135deg, #0a0a0a, #1a1a1a, #2a2a2a); */
            color: white;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
            overflow: hidden;
        }

        /* Stars background */
        .ticket-body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(2px 2px at 20px 30px, #fff, transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(255, 255, 255, 0.8), transparent),
                radial-gradient(1px 1px at 90px 40px, #fff, transparent),
                radial-gradient(1px 1px at 130px 80px, rgba(255, 255, 255, 0.6), transparent),
                radial-gradient(2px 2px at 160px 30px, #fff, transparent);
            background-repeat: repeat;
            background-size: 200px 100px;
            animation: ticket-twinkle 3s infinite;
            z-index: 1;
        }

        @keyframes ticket-twinkle {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .ticket-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
        }

        /* Title */
        .ticket-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .ticket-title-image {
            width: 350px;
            height: 80px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 350 80"><defs><filter id="neon-glow"><feGaussianBlur stdDeviation="4" result="coloredBlur"/><feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><text x="175" y="45" font-family="Arial Black" font-size="32" font-weight="900" text-anchor="middle" fill="white" stroke="rgba(255,255,255,0.8)" stroke-width="2" filter="url(%23neon-glow)">TICKETS</text></svg>') no-repeat center;
            background-size: contain;
            margin: 0 auto;
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8)) drop-shadow(0 0 40px rgba(255, 255, 255, 0.4));
        }

        /* Pricing Cards Container */
        .ticket-pricing-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .ticket-card {
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 0;
            width: 280px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .ticket-card:hover {
            transform: translateY(-10px);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .ticket-header {
            padding: 25px 20px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .ticket-type {
            font-size: 18px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .ticket-price {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 10px;
            padding: 12px 20px;
            border-radius: 8px;
            display: inline-block;
        }

        /* Regular Ticket - Red Theme */
        .ticket-regular .ticket-price {
            background: linear-gradient(135deg, #dc2626, #ef4444, #f87171);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
        }

        /* VIP Ticket - Dark Theme */
        .ticket-vip .ticket-price {
            background: linear-gradient(135deg, #374151, #4b5563, #6b7280);
            color: white;
            box-shadow: 0 4px 15px rgba(75, 85, 99, 0.4);
        }

        /* VVIP Ticket - Dark Theme */
        .ticket-vvip .ticket-price {
            background: linear-gradient(135deg, #1f2937, #374151, #4b5563);
            color: white;
            box-shadow: 0 4px 15px rgba(31, 41, 55, 0.4);
        }

        .ticket-content {
            padding: 25px 20px;
        }

        .ticket-features {
            margin-bottom: 25px;
        }

        .ticket-feature {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .ticket-feature::before {
            content: '•';
            color: rgba(255, 255, 255, 0.6);
            margin-right: 10px;
            font-size: 16px;
        }

        .ticket-button {
            width: 100%;
            padding: 12px 0;
            border: 2px solid;
            border-radius: 8px;
            background: transparent;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ticket-regular .ticket-button {
            border-color: #dc2626;
            color: #dc2626;
        }

        .ticket-regular .ticket-button:hover {
            background: #dc2626;
            color: white;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }

        .ticket-vip .ticket-button,
        .ticket-vvip .ticket-button {
            border-color: rgba(255, 255, 255, 0.4);
            color: rgba(255, 255, 255, 0.8);
        }

        .ticket-vip .ticket-button:hover,
        .ticket-vvip .ticket-button:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.6);
            color: white;
        }

        /* Coming Soon Badge */
        .ticket-coming-soon {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(107, 114, 128, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ticket-pricing-container {
                gap: 20px;
            }

            .ticket-card {
                width: 100%;
                max-width: 300px;
            }

            .ticket-title-image {
                width: 280px;
                height: 60px;
            }
        }

        @media (max-width: 480px) {
            .ticket-card {
                width: 100%;
            }

            .ticket-pricing-container {
                gap: 15px;
            }
        }

        /* Animation */
        .ticket-card {
            animation: ticket-fadeIn 0.6s ease-out;
        }

        .ticket-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .ticket-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .ticket-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes ticket-fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <!-- END nav -->
    <div class="container">
        <div class="left-section">
            <!-- Ganti dengan gambar AOM 11.0 Anda -->
            <div class="aom-title"></div>

            <div class="subtitle"></div>

            <div class="countdown" id="timer">
                <div class="time-box">
                    <div class="digit" id="day">3</div>
                    <div class="countdown-label">Days</div>
                </div>
                <div class="time-box">
                    <div class="digit" id="hour">19</div>
                    <div class="countdown-label">Hours</div>
                </div>
                <div class="time-box">
                    <div class="digit" id="min">19</div>
                    <div class="countdown-label">Minutes</div>
                </div>
                <div class="time-box">
                    <div class="digit" id="sec">19</div>
                    <div class="countdown-label">Seconds</div>
                </div>
            </div>

            <button class="buy-ticket-btn">Buy Ticket Now</buttonc>
        </div>

        <div class="right-section">
            <div class="arena-container">

                <div class="arena-image">
                    <div class="arena-placeholder">
                        <img style="object-fit: cover; width: 100%; height: 110%; scale: 1.1;" src="/images/img1.JPG"
                            alt="">
                    </div>
                </div>

                <div class="dots-indicator">
                    <div class="dot active"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>
    </div>


    {{-- GUESTAR --}}
    <div class="guestar-body">
        <div class="guestar-container">

            <div class="guestar-title">
                <div class="guestar-title-image"></div>
            </div>

            <!-- Carousel -->
            <div class="guestar-carousel">
                <div class="guestar-carousel-wrapper" id="guestarCarouselWrapper">
                    <!-- Slide 1 - Ariana Grande -->
                    <div class="guestar-slide">
                        <div class="guestar-card">
                            <div class="guestar-image guestar-ariana">
                                <img src="/images/gs1_aom11.jpg" alt="">
                            </div>
                            <div class="guestar-info">
                                <div class="guestar-name">Justin Bieber</div>
                                <div class="guestar-label">Gueststar 1</div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 - Ed Sheeran -->
                    <div class="guestar-slide">
                        <div class="guestar-card">
                            <div class="guestar-image guestar-ed">
                                <img src="/images/gs2_aom11.jpg" alt="">
                            </div>
                            <div class="guestar-info">
                                <div class="guestar-name">Taylor Swift</div>
                                <div class="guestar-label">Gueststar 2</div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3 - Adele -->
                    <div class="guestar-slide">
                        <div class="guestar-card">
                            <div class="guestar-image guestar-adele">
                                <img src="/images/gs3_aom11.jpg" alt="">
                            </div>
                            <div class="guestar-info">
                                <div class="guestar-name">Billie Eilish</div>
                                <div class="guestar-label">Gueststar 3</div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4 - Taylor Swift -->
                    <div class="guestar-slide">
                        <div class="guestar-card">
                            <div class="guestar-image guestar-taylor">
                                <img src="/images/gs4_aom11.jpg" alt="">
                            </div>
                            <div class="guestar-info">
                                <div class="guestar-name">Dua Lipa</div>
                                <div class="guestar-label">Gueststar 4</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                {{-- <div class="guestar-nav guestar-prev" onclick="guestarPrevSlide()"></div>
                <div class="guestar-nav guestar-next" onclick="guestarNextSlide()"></div> --}}
            </div>
        </div>
    </div>
    {{-- END GUESTAR --}}
    {{-- PAGE TICKET --}}
    <div id="ticket-body">
        <div class="ticket-container">
            <!-- Title - Ganti dengan gambar TICKETS Anda -->
            <div class="ticket-title">
                <div class="ticket-title-image"></div>
            </div>

            <!-- Pricing Cards -->
            <div class="ticket-pricing-container">
                @foreach ($ticket as $item)
                    @if ($item->quantity > 0)
                        <!-- Regular Ticket -->
                        <div class="ticket-card ticket-regular">
                            <div class="ticket-header">
                                <div class="ticket-type">{{ $item->name }}</div>
                                <div class="ticket-price"> Rp. @currency($item->price)</div>
                            </div>
                            <div class="ticket-content">
                                <div class="ticket-features">
                                    @foreach ($item->ticket_benefit as $benefit)
                                        <li><i class="mr-3"></i></i>{{ $benefit->name }}
                                        </li>
                                    @endforeach
                                </div>
                                @guest
                                    <button type="button" class="ticket-button" style="border-radius: 40px;"
                                        onclick="window.location.href='{{ route('login') }}'">
                                        Buy Ticket
                                    </button>
                                @else
                                    <button class="ticket-button btnModal" style="border-radius: 40px;"
                                        data-bs-toggle="modal" data-bs-target="#beliTicket">
                                        Buy Ticket
                                    </button>
                                @endguest
                            </div>
                        </div>

                        <!-- VIP Ticket -->
                        {{-- <div class="ticket-card ticket-vip">
                            <div class="ticket-coming-soon">Coming Soon</div>
                            <div class="ticket-header">
                                <div class="ticket-type">VIP</div>
                                <div class="ticket-price">SEGERA HADIR</div>
                            </div>
                            <div class="ticket-content">
                                <div class="ticket-features">
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                </div>
                                <button class="ticket-button">Get Tickets</button>
                            </div>
                        </div> --}}

                        <!-- VVIP Ticket -->
                        {{-- <div class="ticket-card ticket-vvip">
                            <div class="ticket-coming-soon">Coming Soon</div>
                            <div class="ticket-header">
                                <div class="ticket-type">VVIP</div>
                                <div class="ticket-price">SEGERA HADIR</div>
                            </div>
                            <div class="ticket-content">
                                <div class="ticket-features">
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                    <div class="ticket-feature">Lorem Ipsum</div>
                                </div>
                                <button class="ticket-button">Get Tickets</button>
                            </div>
                        </div> --}}
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @if (Auth::check())
        <div class="modal fade" id="beliTicket" tabindex="-1" aria-labelledby="beliTicket" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <form id="formTicket" method="post" action="{{ route('buy.ticket') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">Buy Your Ticket</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <b><span class="text-danger">*: WAJIB DIISI</span></b>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Transaction</h5>
                                    <div class="row">
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Username <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-sm-8 col-7">
                                                <p class="form-control-plaintext text-end">
                                                    @if (Auth::check())
                                                        {{ Auth::user()->name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">No
                                                HP <span class="text-danger">*</span></label>
                                            <div class="col-sm-8 col-7">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="no_telp" minlength="10" maxlength="14"
                                                            placeholder="Masukan Nomor HP">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Nama
                                                Ticket <span class="text-danger">*</span></label>
                                            <div class="col-sm-8 col-7">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="ticketName" name="nama_ticket"
                                                            placeholder="Enter Code">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Kode
                                                Panitia</label>
                                            <div class="col-sm-8 col-7">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="kode_panitia" name="kode_panitia"
                                                            placeholder="Enter Code">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-danger btn-sm" type="button"
                                                                id="submitPanitia">Apply</button>
                                                        </div>
                                                    </div>
                                                    <span id="hasilPanitia"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="card-text text-dark" for="kode_voucher">
                                            Kode Voucher :
                                        </label>
                                        <input type="text" class="form-control form-control-sm mb-2"
                                            name="kode_voucher" id="kode_voucher">
                                        <button class="btn btn-danger" type="button"
                                            id="submitVoucher">Apply</button>
                                    </div>
                                </div>
                                <span id="hasilVoucher"></span>
                            </div>
                            <div class="card mt-3">
                                <div class="card-body">
                                    <strong class="card-text text-dark">
                                        Rincian Pembayaran
                                    </strong>
                                    <div class="row">
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Harga
                                                normal</label>
                                            <div class="col-sm-8 col-7">
                                                <p class="form-control-plaintext text-end" id="hargaNormal"></p>
                                            </div>
                                        </div>
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Biaya
                                                Admin</label>
                                            <div class="col-sm-8 col-7">
                                                <p class="form-control-plaintext text-end" id="biayaAdmin"></p>
                                            </div>
                                        </div>
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Harga
                                                Diskon</label>
                                            <div class="col-sm-8 col-7">
                                                <p class="form-control-plaintext text-end" id="hargaDiskon"></p>
                                            </div>
                                        </div>
                                        <div class="form-group row text-dark">
                                            <label for="staticEmail"
                                                class="col-sm-4 col-5 col-form-label text-wrap">Total
                                                Harga</label>
                                            <div class="col-sm-8 col-7">
                                                <p class="form-control-plaintext text-end" id="totalHarga"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" style="border-radius: 40px;"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger" style="border-radius: 40px;">Buat
                                Pesanan</button>
                        </div>
                    </div>
                    <input type="hidden" name="totalHarga" id="totalHargaInput">
                </form>
            </div>
        </div>
    @endif
    {{-- END PAGE TICKET --}}
    {{-- SCRIPT HERO --}}
    <script>
        // Countdown Timer
        function updateCountdown() {
            const now = new Date().getTime();
            const targetDate = new Date(now + (3 * 24 * 60 * 60 * 1000) + (19 * 60 * 60 * 1000) + (19 * 60 * 1000) + (19 *
                1000));

            const distance = targetDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = days;
            document.getElementById('hours').textContent = hours;
            document.getElementById('minutes').textContent = minutes;
            document.getElementById('seconds').textContent = seconds;
        }

        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Dots indicator functionality
        const dots = document.querySelectorAll('.dot');
        let currentDot = 0;

        function updateDots() {
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentDot);
            });
            currentDot = (currentDot + 1) % dots.length;
        }

        // Auto-rotate dots every 3 seconds
        setInterval(updateDots, 3000);

        // Click functionality for dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentDot = index;
                updateDots();
            });
        });
    </script>
    {{-- END SCRIPT HERO --}}
    {{-- SCRIPT GUESTAR --}}
    <script>
        let guestarCurrentSlide = 0;
        const guestarTotalSlides = 4;
        const guestarWrapper = document.getElementById('guestarCarouselWrapper');

        function guestarUpdateCarousel() {
            const guestarTranslateX = -(guestarCurrentSlide * 25);
            guestarWrapper.style.transform = `translateX(${guestarTranslateX}%)`;
        }

        function guestarNextSlide() {
            guestarCurrentSlide = (guestarCurrentSlide + 1) % guestarTotalSlides;
            guestarUpdateCarousel();
        }

        function guestarPrevSlide() {
            guestarCurrentSlide = (guestarCurrentSlide - 1 + guestarTotalSlides) % guestarTotalSlides;
            guestarUpdateCarousel();
        }

        // Auto-slide every 4 seconds
        //setInterval(guestarNextSlide, 4000);

        // Touch/swipe support for mobile
        let guestarStartX = 0;
        let guestarEndX = 0;

        guestarWrapper.addEventListener('touchstart', function(e) {
            guestarStartX = e.touches[0].clientX;
        });

        guestarWrapper.addEventListener('touchmove', function(e) {
            guestarEndX = e.touches[0].clientX;
        });

        guestarWrapper.addEventListener('touchend', function() {
            const guestarDiff = guestarStartX - guestarEndX;

            if (Math.abs(guestarDiff) > 50) {
                if (guestarDiff > 0) {
                    guestarNextSlide();
                } else {
                    guestarPrevSlide();
                }
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                guestarPrevSlide();
            } else if (e.key === 'ArrowRight') {
                guestarNextSlide();
            }
        });
    </script>
    {{-- END SCRIPT GUESTAR --}}
    {{-- SCRIPT TICKET --}}

    {{-- END SCRIPT TICKET --}}
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/jquery.timepicker.min.js"></script>
    <script src="js/scrollax.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
    <script src="js/google-map.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let totalHSBA = 0;
            let potonganAdmin = 0;
            let biayaAdmin = 0;
            let hargaTiket = 0;
            $("#payment_method").prop("selectedIndex", 0);
            const formatNumber = new Intl.NumberFormat("id-ID", {
                style: 'currency',
                currency: 'IDR'
            });

            function parseCurrency(currencyString) {
                return parseFloat(currencyString.replace(/Rp\s*/, '').replace(/\./g, '').replace(/,/g, '.'));
            }

            function parseFormattedNumber(formattedNumber) {
                return parseInt(formattedNumber.replace(/\./g, ''), 10);
            }

            function updateText(selector, value) {
                $(selector).text(formatNumber.format(value));
            }

            function handleAjaxError(jqXHR, textStatus, errorThrown) {}

            updateText("#biayaAdmin", 0);
            updateText("#hargaDiskon", 0);

            $("#submitPanitia").click(function() {
                const idPanitia = $("#kode_panitia").val();
                $.get(`getPanitia/${idPanitia}`)
                    .done(function(item) {
                        if (item.data) {
                            $("#kode_panitia").prop('readonly', true);
                            $("#submitPanitia").hide();
                            alert("Berhasil");
                        } else {
                            alert("Kode Tidak Ditemukan");
                        }
                    })
                    .fail(handleAjaxError);
            });

            $("#submitVoucher").click(calcVoucher);

            $("#payment-method").change(function() {
                const idMethod = $(this).val();
                const methodToAdmin = {
                    "bca": 0,
                    "mandiri": 0,
                };

                if (methodToAdmin.hasOwnProperty(idMethod)) {
                    potonganAdmin = methodToAdmin[idMethod];
                    calcBiayaAdmin(potonganAdmin);
                } else {
                    alert("Terjadi kesalahan");
                }
            });

            $(".btnModal").click(function() {
                const $pricingS1 = $(this).closest('.pricing-s1');
                const ticketName = $pricingS1.find(".ticket-name").text().trim();
                hargaTiket = parseFormattedNumber($pricingS1.find(".hargaTiket").text().trim());

                $("#ticketName").val(ticketName).prop('readonly', true);
                updateText("#hargaNormal", hargaTiket);
                updateText("#totalHarga", hargaTiket);
            });

            $('#modalTiket').on('hidden.bs.modal', function() {
                $('#formTicket')[0].reset();
            });

            function calcBiayaAdmin(potonganAdmin) {
                biayaAdmin = potonganAdmin < 1 ? hargaTiket * potonganAdmin : potonganAdmin;
                totalHSBA = hargaTiket + biayaAdmin;
                calcVoucher();
                updateText("#biayaAdmin", biayaAdmin);
                updateText("#totalHarga", totalHSBA);
            }

            function calcVoucher() {
                const idVoucher = $("#kode_voucher").val();
                $.get(`getVoucher/${idVoucher}`)
                    .done(function(item) {
                        if (item.data) {
                            const hargaDiskon = item.data.discount;
                            let totalHarga = totalHSBA - hargaDiskon;
                            if (totalHarga < 1) totalHarga = hargaTiket - hargaDiskon;

                            if (totalHarga >= 10000) {
                                $("#kode_voucher").prop('readonly', true);
                                updateText("#hargaDiskon", hargaDiskon);
                                updateText("#totalHarga", totalHarga);
                                $("#submitVoucher").hide();
                            } else {
                                alert("Kode Voucher Tidak Dapat Dipakai");
                                $("#kode_voucher").val("");
                            }
                        } else {
                            alert("Kode Voucher Tidak Valid");
                            $("#kode_voucher").val("");
                        }
                    })
                    .fail(handleAjaxError);
            }

            $('#formTicket').submit(function(e) {
                e.preventDefault();

                // Ambil nilai total harga dengan cara yang lebih akurat
                const totalHargaText = $('#totalHarga').text().trim();

                // Bersihkan format currency (untuk format "Rp 60.000,00")
                const totalHarga = parseFloat(
                    totalHargaText
                    .replace(/Rp\s?/g, '')
                    .replace(/\./g, '')
                    .replace(/,/g, '.')
                );

                console.log('Total Harga (Cleaned):', totalHarga); // Harusnya 60000

                // Validasi
                if (isNaN(totalHarga) || totalHarga < 10000) {
                    alert('Harga tidak valid!');
                    return;
                }

                // Set nilai ke hidden input
                $('#totalHargaInput').val(totalHarga);

                // Lanjutkan dengan AJAX submit
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success && response.snapToken) {
                            window.location.href = '/payment?snap_token=' + response.snapToken;
                        } else {
                            alert('Error: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Server error'));
                    }
                });
            });
        });
    </script>
</body>

</html>
