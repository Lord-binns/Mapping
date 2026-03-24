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

        #progressbar {
            margin: 0 0 30px;
            padding: 0;
            overflow: hidden;
            counter-reset: step;
            display: flex;
        }

        #progressbar li {
            list-style-type: none;
            color: #0b6d5a;
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 700;
            width: 33.33%;
            position: relative;
            letter-spacing: 0.04em;
        }

        #progressbar li::before {
            content: counter(step);
            counter-increment: step;
            width: 24px;
            line-height: 24px;
            display: block;
            font-size: 11px;
            color: #0b6d5a;
            background: #ffffff;
            border: 1px solid #bfe9dc;
            border-radius: 6px;
            margin: 0 auto 8px;
        }

        #progressbar li::after {
            content: '';
            width: 100%;
            height: 2px;
            background: #bfe9dc;
            position: absolute;
            left: -50%;
            top: 11px;
            z-index: -1;
        }

        #progressbar li:first-child::after {
            content: none;
        }

        #progressbar li.active::before,
        #progressbar li.active::after {
            background: #00c9a2;
            color: #ffffff;
            border-color: #00c9a2;
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

        #msform fieldset:not(:first-of-type) {
            display: none;
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
    <form id="msform" action="#" method="get">
        <ul id="progressbar">
            <li class="active">Account Setup</li>
            <li>Social Profiles</li>
            <li>Personal Details</li>
        </ul>

        <fieldset>
            <h2 class="fs-title">Create Your Account</h2>
            <h3 class="fs-subtitle">This is step 1</h3>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="pass" placeholder="Password" required>
            <input type="password" name="cpass" placeholder="Confirm Password" required>
            <input type="button" name="next" class="next action-button" value="Next">
            <div class="top-links">
                <a href="{{ url('/') }}">Back Home</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
        </fieldset>

        <fieldset>
            <h2 class="fs-title">Social Profiles</h2>
            <h3 class="fs-subtitle">Your presence on the social network</h3>
            <input type="text" name="twitter" placeholder="Twitter">
            <input type="text" name="facebook" placeholder="Facebook">
            <input type="text" name="gplus" placeholder="Google Plus">
            <input type="button" name="previous" class="previous action-button secondary" value="Previous">
            <input type="button" name="next" class="next action-button" value="Next">
        </fieldset>

        <fieldset>
            <h2 class="fs-title">Personal Details</h2>
            <h3 class="fs-subtitle">We will never sell it</h3>
            <input type="text" name="fname" placeholder="First Name" required>
            <input type="text" name="lname" placeholder="Last Name" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="button" name="previous" class="previous action-button secondary" value="Previous">
            <button type="submit" class="action-button">Submit</button>
            <div class="top-links">
                <a href="{{ route('login') }}">Already have an account</a>
            </div>
        </fieldset>
    </form>

    <script>
        (function () {
            const form = document.getElementById('msform');
            if (!form) return;

            const fieldsets = Array.from(form.querySelectorAll('fieldset'));
            const progressSteps = Array.from(document.querySelectorAll('#progressbar li'));
            let currentIndex = 0;

            function showStep(index) {
                fieldsets.forEach((fieldset, i) => {
                    fieldset.style.display = i === index ? 'block' : 'none';
                });

                progressSteps.forEach((step, i) => {
                    step.classList.toggle('active', i <= index);
                });

                currentIndex = index;
            }

            form.addEventListener('click', function (event) {
                const target = event.target;

                if (target.classList.contains('next')) {
                    event.preventDefault();
                    if (currentIndex < fieldsets.length - 1) {
                        showStep(currentIndex + 1);
                    }
                }

                if (target.classList.contains('previous')) {
                    event.preventDefault();
                    if (currentIndex > 0) {
                        showStep(currentIndex - 1);
                    }
                }
            });

            showStep(0);
        })();
    </script>
</body>
</html>
