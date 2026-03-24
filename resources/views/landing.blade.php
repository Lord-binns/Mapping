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
            padding-top: 94px;
            padding-bottom: 24px;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            min-height: 74px;
            background: rgba(255, 255, 255, 0.92);
            border-bottom: 1px solid #d9d9d9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 10px 18px;
            z-index: 20;
            backdrop-filter: blur(6px);
        }

        .brand {
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #111;
            font-size: 14px;
            white-space: nowrap;
        }

        .main-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #1c1c1c;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 9px 10px;
            border-radius: 999px;
            border: 1px solid transparent;
        }

        .nav-link svg {
            width: 14px;
            height: 14px;
            stroke-width: 2;
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
            padding: 8px 12px;
            font-size: 12px;
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
            padding: 7px 12px;
            font-size: 11px;
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
                min-height: 74px;
                flex-direction: column;
                align-items: stretch;
                padding: 10px 12px;
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
                padding-top: 112px;
            }

            .insight-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body data-page="globe-map">
    <header class="navbar">
        <div class="brand">Clean Earth Interactive Mapping</div>
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

            <div class="nav-item-controls">
                <button type="button" class="controls-trigger">Controls</button>
                <div class="controls-dropdown" id="top-controls" aria-label="Country shortcuts">
                    <button type="button" data-country="Philippines" class="is-active">Philippines</button>
                    <button type="button" data-country="United States">United States</button>
                    <button type="button" data-country="Australia">Australia</button>
                    <button type="button" data-country="Brazil">Brazil</button>
                    <button type="button" data-country="India">India</button>
                    <button type="button" data-country="Japan">Japan</button>
                </div>
            </div>

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

        <section class="section-lite" id="about">
            <h2>About Us</h2>
            <p>
                Clean Earth Interactive Mapping is a visual monitoring platform that helps communities, responders, and local agencies understand environmental activity at a glance.
            </p>
        </section>

        <section class="new-section" id="features">
            <h2>Live Environmental Intelligence</h2>
            <p>
                This section highlights how Clean Earth Interactive Mapping supports proactive monitoring and faster response across different regions.
            </p>
            <div class="insight-grid">
                <article class="insight-card">
                    <h3>Flood Monitoring</h3>
                    <p>
                        Track flood-prone zones in near real-time and surface high-risk locations for rapid emergency coordination.
                    </p>
                </article>
                <article class="insight-card">
                    <h3>Waste Hotspots</h3>
                    <p>
                        Identify illegal dumping clusters and recurring waste incidents to improve route planning and cleanup operations.
                    </p>
                </article>
                <article class="insight-card">
                    <h3>Water Quality Watch</h3>
                    <p>
                        Monitor contamination alerts by region and prioritize inspections using map-based severity indicators.
                    </p>
                </article>
            </div>
        </section>

        <section class="section-lite" id="contact">
            <h2>Contact</h2>
            <p>
                Reach the mapping team for integrations, pilot deployments, or data partnerships to expand environmental monitoring coverage.
            </p>
        </section>
    </main>

    <footer class="footer">Clean Earth Interactive Mapping</footer>

    <svg id="map" viewBox="0 0 1010 666"></svg>
    <svg id="country" viewBox="0 0 1010 666"></svg>
</body>
</html>
