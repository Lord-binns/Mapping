<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clean Earth Interactive Mapping</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            padding: 0;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: sans-serif;
            background: #f4f4f4;
        }

        .main-content {
            padding-top: 120px;
            padding-bottom: 24px;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            min-height: 80px;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 3px solid #00c9a2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 24px;
            z-index: 20;
            backdrop-filter: blur(6px);
        }

        .brand {
            font-weight: 900;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #0b6d5a;
            font-size: 28px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00c9a2 0%, #0b6d5a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 16px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .main-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #1c1c1c;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid transparent;
        }

        .nav-link svg {
            width: 16px;
            height: 16px;
            stroke-width: 2;
            stroke: currentColor;
            flex-shrink: 0;
        }

        .nav-link:hover {
            border-color: #d4d4d4;
            background: #ffffff;
        }

        .nav-auth {
            border: 1px solid #cbcbcb;
            background: #ffffff;
        }

        .nav-auth:hover {
            border-color: #00c9a2;
            color: #0b6d5a;
            background: #ecfffb;
        }

        .nav-item-controls {
            position: relative;
        }

        .controls-trigger {
            border: 1px solid #cbcbcb;
            background: #ffffff;
            color: #222;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 13px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            cursor: pointer;
        }

        .controls-trigger:hover,
        .nav-item-controls:focus-within .controls-trigger {
            border-color: #00c9a2;
            color: #0b6d5a;
            background: #ecfffb;
        }

        .controls-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: min(430px, 86vw);
            display: none;
            gap: 8px;
            flex-wrap: wrap;
            padding: 10px;
            border: 1px solid #d5d5d5;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            z-index: 25;
        }

        .nav-item-controls:hover .controls-dropdown,
        .nav-item-controls:focus-within .controls-dropdown {
            display: flex;
        }

        .controls-dropdown button {
            border: 1px solid #cbcbcb;
            background: #ffffff;
            color: #222;
            border-radius: 999px;
            padding: 8px 13px;
            font-size: 12px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            cursor: pointer;
        }

        .controls-dropdown button:hover,
        .controls-dropdown button.is-active {
            border-color: #00c9a2;
            color: #0b6d5a;
            background: #ecfffb;
        }

        .page {
            width: 100%;
            min-height: calc(100vh - 140px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 64px;
            position: relative;
            z-index: 1;
        }

        .globe-wrapper {
            margin-top: 3vh;
            position: relative;
            width: min(92vw, 700px);
            height: min(92vw, 700px);
        }

        .info {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        .info span {
            font-weight: 700;
            text-shadow: 0 0 5px #ffffff;
            padding: 0.2em 0.6em;
            border-radius: 2px;
            font-size: clamp(24px, 4vw, 38px);
            color: #0f0f0f;
        }

        canvas {
            width: 100%;
            height: 100%;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        svg {
            position: fixed;
            top: 0;
            visibility: hidden;
        }

        .lil-gui {
            --width: 350px;
            max-width: 90%;
            --widget-height: 20px;
            font-size: 15px;
            --input-font-size: 15px;
            --padding: 10px;
            --spacing: 10px;
            --slider-knob-width: 5px;
            --background-color: rgba(5, 0, 15, 0.8);
            --widget-color: rgba(255, 255, 255, 0.3);
            --focus-color: rgba(255, 255, 255, 0.4);
            --hover-color: rgba(255, 255, 255, 0.5);
            --font-family: monospace;
        }

        .footer {
            position: relative;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.88);
            border-top: 1px solid #d9d9d9;
            color: #3c3c3c;
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            z-index: 20;
        }

        .new-section {
            width: min(1100px, 92vw);
            margin: 20px auto 26px;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 14px;
            padding: 22px;
        }

        .new-section h2 {
            margin: 0 0 8px;
            font-size: clamp(22px, 2.8vw, 32px);
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #1a1a1a;
        }

        .new-section p {
            margin: 0 0 18px;
            color: #4b4b4b;
            max-width: 900px;
            line-height: 1.5;
        }

        .insight-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .insight-card {
            border: 1px solid #e2e2e2;
            border-radius: 10px;
            background: #fbfbfb;
            padding: 14px;
        }

        .insight-card h3 {
            margin: 0 0 6px;
            font-size: 14px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #0e5f63;
        }

        .insight-card p {
            margin: 0;
            font-size: 13px;
            line-height: 1.45;
            color: #444;
        }

        .section-lite {
            width: min(1100px, 92vw);
            margin: 0 auto 20px;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 14px;
            padding: 20px;
        }

        .section-lite h2 {
            margin: 0 0 8px;
            font-size: 20px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .section-lite p {
            margin: 0;
            color: #4b4b4b;
            line-height: 1.5;
        }

        @media (max-width: 900px) {
            .navbar {
                height: auto;
                min-height: 100px;
                flex-direction: column;
                align-items: stretch;
                padding: 16px 12px;
                gap: 8px;
            }

            .main-nav {
                justify-content: flex-start;
            }

            .controls-dropdown {
                position: static;
                width: 100%;
                display: flex;
                box-shadow: none;
                margin-top: 6px;
            }

            .nav-item-controls {
                width: 100%;
            }

            .controls-trigger {
                width: 100%;
                text-align: left;
            }

            .page {
                min-height: calc(100vh - 170px);
            }

            .main-content {
                padding-top: 140px;
            }

            .insight-grid {
                grid-template-columns: 1fr;
            }
        }

        .carousel-container {
            width: 100%;
            height: 100vh;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: #f4f4f4;
            position: relative;
        }

        .blank-middle-section {
            width: min(1100px, 92vw);
            min-height: 220px;
            margin: 22px auto;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 14px;
        }

        .slider {
            list-style-type: none;
            margin: 0;
            padding: 0;
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slider .item {
            width: 200px;
            height: 300px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
            background-position: center;
            background-size: cover;
            border-radius: 20px;
            box-shadow: 0 20px 30px rgba(255, 255, 255, 0.3) inset;
            transition: transform 0.1s, left 0.75s, top 0.75s, width 0.75s, height 0.75s;
        }

        .slider .item:nth-child(1),
        .slider .item:nth-child(2) {
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            transform: none;
            border-radius: 0;
            box-shadow: none;
            opacity: 1;
        }

        .slider .item:nth-child(3) { left: 50%; }
        .slider .item:nth-child(4) { left: calc(50% + 220px); }
        .slider .item:nth-child(5) { left: calc(50% + 440px); }
        .slider .item:nth-child(6) { left: calc(50% + 660px); opacity: 0; }

        .carousel-content {
            width: min(30vw, 400px);
            position: absolute;
            top: 50%;
            left: 3rem;
            transform: translateY(-50%);
            font: 400 0.85rem helvetica, sans-serif;
            color: white;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.5);
            opacity: 0;
            display: none;
        }

        .carousel-content .carousel-title {
            font-family: 'Arial Black', sans-serif;
            text-transform: uppercase;
            margin: 0 0 1rem 0;
            font-size: 1.4rem;
        }

        .carousel-content .carousel-description {
            line-height: 1.7;
            margin: 1rem 0 1.5rem 0;
            font-size: 0.8rem;
        }

        .carousel-content button {
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.1);
            color: white;
            border: 2px solid white;
            border-radius: 0.25rem;
            padding: 0.75rem 1rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .carousel-content button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .slider .item:nth-of-type(2) .carousel-content {
            display: block;
            animation: carousel-show 0.75s ease-in-out 0.3s forwards;
        }

        @keyframes carousel-show {
            0% {
                filter: blur(5px);
                transform: translateY(calc(-50% + 75px));
            }
            100% {
                opacity: 1;
                filter: blur(0);
            }
        }

        .carousel-nav {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            user-select: none;
            display: flex;
            gap: 1rem;
        }

        .carousel-nav .carousel-btn {
            background-color: rgba(255, 255, 255, 0.5);
            color: rgba(0, 0, 0, 0.7);
            border: 2px solid rgba(0, 0, 0, 0.6);
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        .carousel-nav .carousel-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 900px) {
            .carousel-container {
                height: 80vh;
            }
            .slider .item {
                width: 160px;
                height: 270px;
            }
            .slider .item:nth-child(3) { left: 50%; }
            .slider .item:nth-child(4) { left: calc(50% + 170px); }
            .slider .item:nth-child(5) { left: calc(50% + 340px); }
            .slider .item:nth-child(6) { left: calc(50% + 510px); opacity: 0; }
            .carousel-content {
                width: min(25vw, 350px);
            }
            .carousel-content .carousel-title { font-size: 1rem; }
            .carousel-content .carousel-description { font-size: 0.7rem; }
            .carousel-content button { font-size: 0.7rem; }
        }

        @media (max-width: 650px) {
            .carousel-container {
                height: 70vh;
            }
            .carousel-content {
                width: min(20vw, 300px);
            }
            .carousel-content .carousel-title { font-size: 0.9rem; }
            .carousel-content .carousel-description { font-size: 0.65rem; }
            .carousel-content button { font-size: 0.7rem; }
            .slider .item {
                width: 130px;
                height: 220px;
            }
            .slider .item:nth-child(3) { left: 50%; }
            .slider .item:nth-child(4) { left: calc(50% + 140px); }
            .slider .item:nth-child(5) { left: calc(50% + 280px); }
            .slider .item:nth-child(6) { left: calc(50% + 420px); opacity: 0; }
        }
    </style>
</head>
<body data-page="globe-map">
    <header class="navbar">
        <div class="brand">
            <div class="brand-logo"><img src="https://via.placeholder.com/40" alt="Logo"></div>
            <span>Clean Earth Interactive Mapping</span>
        </div>
        <nav class="main-nav" aria-label="Main navigation">
            <a class="nav-link" href="#home">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 9.5V20h13V9.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Home
            </a>
            <a class="nav-link" href="#about">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7.5" r="3" stroke="currentColor"/><path d="M5 20c.7-3.1 3.4-5 7-5s6.3 1.9 7 5" stroke="currentColor" stroke-linecap="round"/></svg>
                About Us
            </a>
            <a class="nav-link" href="#features">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-linecap="round"/></svg>
                Features
            </a>
            <a class="nav-link" href="#contact">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4z" stroke="currentColor"/><path d="M4 8l8 5 8-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Contact
            </a>

            {{--  <div class="nav-item-controls">
                <button type="button" class="controls-trigger">Controls</button>
                <div class="controls-dropdown" id="top-controls" aria-label="Country shortcuts">
                    <button type="button" data-country="Philippines" class="is-active">Philippines</button>
                    <button type="button" data-country="United States">United States</button>
                    <button type="button" data-country="Australia">Australia</button>
                    <button type="button" data-country="Brazil">Brazil</button>
                    <button type="button" data-country="India">India</button>
                    <button type="button" data-country="Japan">Japan</button>
                </div>
            </div>  --}}

            <a class="nav-link nav-auth" href="{{ route('login') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12H3" stroke="currentColor" stroke-linecap="round"/><path d="M21 4v16" stroke="currentColor" stroke-linecap="round"/></svg>
                Login
            </a>
            <a class="nav-link nav-auth" href="{{ route('register') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="currentColor"/></svg>
                Register
            </a>
        </nav>
    </header>

    <main class="main-content">
        <div class="page" id="home">
            <div class="globe-wrapper">
                <canvas id="globe-3d"></canvas>
                <div class="info"><span></span></div>
            </div>
        </div>
{{--  
        <section class="blank-middle-section" aria-label="Blank spacer section">
        
        
        </section>  --}}

        <div class="carousel-container">
            <ul class="slider" id="carousel-slider">
                <li class="item" style="background-image: url('https://i.pinimg.com/736x/6f/88/bc/6f88bc51f7005f59f998333655a803cd.jpg')">
                    <div class="carousel-content">
                        <h2 class="carousel-title">Flood Monitoring</h2>
                        <p class="carousel-description">Real-time flood detection and early warning systems help communities prepare for natural disasters and minimize impact on vulnerable areas.</p>
                        <button>Learn More</button>
                    </div>
                </li>
                <li class="item" style="background-image: url('https://i.pinimg.com/1200x/9d/ae/65/9dae657348b13d1e2156a2142dca954e.jpg')">
                    <div class="carousel-content">
                        <h2 class="carousel-title">Waste Hotspots</h2>
                        <p class="carousel-description">Identify and track illegal dumping sites with satellite imagery and machine learning to improve cleanup operations and protect the environment.</p>
                        <button>Learn More</button>
                    </div>
                </li>
                <li class="item" style="background-image: url('https://i.pinimg.com/736x/d4/82/e1/d482e150a3741faad395b611592c53ea.jpg')">
                    <div class="carousel-content">
                        <h2 class="carousel-title">Water Quality Watch</h2>
                        <p class="carousel-description">Monitor water contamination levels across regions with real-time alerts to ensure safe water supply and aquatic ecosystem health.</p>
                        <button>Learn More</button>
                    </div>
                </li>
                <li class="item" style="background-image: url('https://i.pinimg.com/1200x/72/bd/7c/72bd7c0c8b7913e9da860c152a480268.jpg')">
                    <div class="carousel-content">
                        <h2 class="carousel-title">Air Quality Index</h2>
                        <p class="carousel-description">Track air pollution patterns and provide actionable insights to reduce emissions and improve public health outcomes in urban areas.</p>
                        <button>Learn More</button>
                    </div>
                </li>
                <li class="item" style="background-image: url('https://i.pinimg.com/736x/d4/82/e1/d482e150a3741faad395b611592c53ea.jpg')">
                    <div class="carousel-content">
                        <h2 class="carousel-title">Forest Coverage</h2>
                        <p class="carousel-description">Monitor deforestation rates and track reforestation efforts to promote sustainable forest management and biodiversity conservation.</p>
                        <button>Learn More</button>
                    </div>
                </li>
                <li class="item" style="background-image: url('https://i.pinimg.com/1200x/79/99/0f/79990f6daab114b9ef2b37b25b797080.jpg')">
                    <div class="carousel-content">
                        <h2 class="carousel-title">Climate Resilience</h2>
                        <p class="carousel-description">Build adaptive capacity and reduce climate change impacts through data-driven planning and community-focused resilience initiatives.</p>
                        <button>Learn More</button>
                    </div>
                </li>
            </ul>
            <nav class="carousel-nav">
                <button class="carousel-btn prev" aria-label="Previous slide">❮</button>
                <button class="carousel-btn next" aria-label="Next slide">❯</button>
            </nav>
        </div>

        
    </main>

    <footer class="footer">Clean Earth Interactive Mapping</footer>

    <svg id="map" viewBox="0 0 1010 666"></svg>
    <svg id="country" viewBox="0 0 1010 666"></svg>

    <script>
        const slider = document.getElementById('carousel-slider');
        const carouselBtns = document.querySelectorAll('.carousel-btn');

        function activateCarousel(e) {
            const items = document.querySelectorAll('.slider .item');
            if (e.target.matches('.next')) {
                slider.appendChild(items[0]);
            }
            if (e.target.matches('.prev')) {
                slider.insertBefore(items[items.length - 1], items[0]);
            }
        }

        carouselBtns.forEach(btn => {
            btn.addEventListener('click', activateCarousel);
        });
    </script>
</body>
</html>
