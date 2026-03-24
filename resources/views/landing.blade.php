<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleanEarth Sentinel | Globe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            padding: 0;
            margin: 0;
            overflow: hidden;
            font-family: sans-serif;
            background: #f4f4f4;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 74px;
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

        .top-controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .top-controls button {
            border: 1px solid #cbcbcb;
            background: #ffffff;
            color: #222;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            cursor: pointer;
        }

        .top-controls button:hover,
        .top-controls button.is-active {
            border-color: #00c9a2;
            color: #0b6d5a;
            background: #ecfffb;
        }

        .page {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
            padding-top: 74px;
            padding-bottom: 44px;
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
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
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

        @media (max-width: 900px) {
            .navbar {
                height: auto;
                min-height: 74px;
                flex-direction: column;
                align-items: stretch;
                padding: 10px 12px;
                gap: 8px;
            }

            .top-controls {
                justify-content: flex-start;
            }

            .page {
                padding-top: 108px;
            }
        }
    </style>
</head>
<body data-page="globe-map">
    <header class="navbar">
        <div class="brand">CleanEarth Sentinel</div>
        <nav class="top-controls" id="top-controls" aria-label="Country shortcuts">
            <button type="button" data-country="Philippines" class="is-active">Philippines</button>
            <button type="button" data-country="United States">United States</button>
            <button type="button" data-country="Australia">Australia</button>
            <button type="button" data-country="Brazil">Brazil</button>
            <button type="button" data-country="India">India</button>
            <button type="button" data-country="Japan">Japan</button>
        </nav>
    </header>

    <div class="page">
        <div class="globe-wrapper">
            <canvas id="globe-3d"></canvas>
            <div class="info"><span></span></div>
        </div>
    </div>

    <footer class="footer">Global Monitoring Visual Prototype</footer>

    <svg id="map" viewBox="0 0 1010 666"></svg>
    <svg id="country" viewBox="0 0 1010 666"></svg>
</body>
</html>
