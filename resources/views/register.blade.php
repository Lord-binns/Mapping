<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Clean Earth Interactive Mapping</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #eefcf8, #f7f8fb);
            font-family: sans-serif;
            color: #1a1a1a;
            padding: 24px;
        }

        .card {
            width: min(520px, 94vw);
            background: #ffffff;
            border: 1px solid #dde4e3;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 6px;
            font-size: 30px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        p {
            margin: 0 0 18px;
            color: #4d4d4d;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .field {
            margin-bottom: 12px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 12px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #3c3c3c;
        }

        input {
            width: 100%;
            border: 1px solid #ccd4d3;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            border: 1px solid #00c9a2;
            background: #00c9a2;
            color: #ffffff;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-decoration: none;
            cursor: pointer;
        }

        .btn.alt {
            background: #ffffff;
            color: #1f1f1f;
            border-color: #cdd6d4;
        }

        @media (max-width: 700px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Register</h1>
        <p>Create your Clean Earth Interactive Mapping account.</p>

        <form action="#" method="get">
            <div class="grid">
                <div class="field">
                    <label for="first_name">First Name</label>
                    <input id="first_name" name="first_name" type="text" placeholder="First name" required>
                </div>
                <div class="field">
                    <label for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" type="text" placeholder="Last name" required>
                </div>
                <div class="field full">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Create password" required>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat password" required>
                </div>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Create Account</button>
                <a class="btn alt" href="{{ route('login') }}">Already Have Account</a>
                <a class="btn alt" href="{{ url('/') }}">Back Home</a>
            </div>
        </form>
    </main>
</body>
</html>
