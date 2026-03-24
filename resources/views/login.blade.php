<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Clean Earth Interactive Mapping</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #f2fffb 0%, #e7fbf5 52%, #ffffff 100%);
            font-family: sans-serif;
            color: #1b1b1b;
            padding: 24px;
        }

        #msform {
            width: min(520px, 96vw);
            text-align: center;
            position: relative;
        }

        #msform fieldset {
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 12px;
            box-shadow: 0 14px 28px rgba(0, 121, 101, 0.1);
            padding: 24px 26px;
            box-sizing: border-box;
            width: 100%;
            position: relative;
        }

        .fs-title {
            margin: 0 0 8px;
            font-size: 20px;
            text-transform: uppercase;
            color: #0b6d5a;
            letter-spacing: 0.03em;
        }

        .fs-subtitle {
            margin: 0 0 18px;
            font-size: 13px;
            color: #55706a;
            font-weight: 400;
        }

        #msform input {
            padding: 12px 14px;
            border: 1px solid #c9dcd7;
            border-radius: 8px;
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
            font-family: sans-serif;
            color: #2c3e50;
            font-size: 14px;
            background: #ffffff;
        }

        .action-button {
            min-width: 110px;
            background: #00c9a2;
            font-weight: 700;
            color: #ffffff;
            border: 1px solid #00c9a2;
            border-radius: 999px;
            cursor: pointer;
            padding: 11px 16px;
            margin: 10px 5px 0;
            text-decoration: none;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .action-button:hover,
        .action-button:focus {
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #00c9a2;
        }

        .action-button.secondary {
            background: #ffffff;
            color: #0b6d5a;
            border-color: #8edac9;
        }

        .top-links {
            margin-top: 12px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .top-links a {
            color: #0b6d5a;
            font-size: 12px;
            text-decoration: none;
            border-bottom: 1px solid transparent;
        }

        .top-links a:hover {
            border-color: #0b6d5a;
        }

        @media (max-width: 650px) {
            body {
                padding: 14px;
            }

            #msform fieldset {
                padding: 18px;
            }

            .fs-title {
                font-size: 17px;
            }
        }
    </style>
</head>
<body>
    <form id="msform" action="{{ route('dashboard') }}" method="get">
        <fieldset>
            <h2 class="fs-title">Login</h2>
            <h3 class="fs-subtitle">Access your Clean Earth Interactive Mapping dashboard</h3>
            <input id="email" name="email" type="email" placeholder="Email" required>
            <input id="password" name="password" type="password" placeholder="Password" required>
            <button class="action-button" type="submit">Log In</button>
            <a class="action-button secondary" href="{{ route('register') }}">Create Account</a>
            <div class="top-links">
                <a href="{{ url('/') }}">Back Home</a>
                <a href="{{ route('register') }}">Register</a>
            </div>
        </fieldset>
    </form>
</body>
</html>
