<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>About Us | {{ config('app.name') }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<style>
		body {
			padding: 0;
			margin: 0;
			overflow-x: hidden;
			overflow-y: auto;
			font-family: sans-serif;
			background: #f4f4f4;
			color: #1b1b1b;
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
			color: #0b6d5a;
			white-space: normal;
			line-height: 1.1;
			max-width: min(820px, 52vw);
			display: inline-flex;
			align-items: center;
			gap: 10px;
			min-width: 0;
		}

		.brand-text {
			display: inline-flex;
			flex-direction: row;
			align-items: center;
			gap: 10px;
			min-width: 0;
		}

		.brand-divider {
			width: 5px;
			height: 50px;
			border-radius: 999px;
			background: #f8b803;
			flex-shrink: 0;
		}

		.brand-main {
			font-size: clamp(24px, 2.4vw, 36px);
			letter-spacing: 0.04em;
			text-transform: uppercase;
			white-space: nowrap;
			line-height: 1;
		}

		.brand-sub {
			font-size: clamp(13px, 1.2vw, 17px);
			font-weight: 700;
			letter-spacing: 0.03em;
			text-transform: uppercase;
			line-height: 1.15;
			color: #2e6d61;
			white-space: nowrap;
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
			font-size: 15px;
			line-height: 1;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			padding: 12px 15px;
			border-radius: 999px;
			border: 1px solid transparent;
		}

		.nav-link svg {
			width: 18px;
			height: 18px;
			stroke-width: 2;
			stroke: currentColor;
			color: #0b6d5a;
			flex-shrink: 0;
		}

		.nav-link:hover,
		.nav-link.is-active {
			border-color: #d4d4d4;
			background: #ffffff;
			color: #0b6d5a;
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
			line-height: 1.6;
		}

		.agency-badge {
			display: inline-flex;
			align-items: center;
			gap: 10px;
			padding: 8px 12px;
			border: 1px solid #dce5e1;
			border-radius: 10px;
			background: #f9fdfc;
			margin-bottom: 14px;
		}

		.agency-badge img {
			width: 38px;
			height: 38px;
			object-fit: contain;
		}

		.agency-badge span {
			font-size: 12px;
			letter-spacing: 0.03em;
			text-transform: uppercase;
			color: #355b54;
			font-weight: 700;
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
			line-height: 1.5;
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

		.section-lite p,
		.section-lite li {
			color: #4b4b4b;
			line-height: 1.6;
		}

		.hero-image {
			width: 100%;
			height: 520px;
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			margin: 0 auto 24px;
			border-radius: 14px;
			box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
			max-width: min(1100px, 92vw);
		}

		.footer {
			position: relative;
			min-height: 34px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: rgba(255, 255, 255, 0.88);
			border-top: 1px solid #d9d9d9;
			color: #3c3c3c;
			font-size: 11px;
			line-height: 1.45;
			text-align: center;
			padding: 8px 12px;
			letter-spacing: 0.04em;
			text-transform: uppercase;
			z-index: 20;
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

			.main-content {
				padding-top: 140px;
			}

			.insight-grid {
				grid-template-columns: 1fr;
			}

			.brand-main {
				font-size: clamp(19px, 3.6vw, 28px);
			}

			.brand-sub {
				font-size: clamp(11px, 2.2vw, 15px);
				white-space: normal;
			}

			.brand-divider {
				height: 40px;
			}
		}
	</style>
</head>
<body>
	<header class="navbar">
		<div class="brand">
			<div class="brand-logo"><img src="https://img.freepik.com/premium-vector/environment-logo-vector_1277164-17389.jpg?w=360" alt="Logo"></div>
			<span class="brand-text">
				<span class="brand-main">ENVIROTRACK</span>
				<span class="brand-divider" aria-hidden="true"></span>
				<span class="brand-sub">Department of Environment<br>and Natural Resources</span>
			</span>
		</div>
		<nav class="main-nav" aria-label="Main navigation">
			<a class="nav-link" href="{{ url('/') }}#home">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 9.5V20h13V9.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
				Home
			</a>
			<a class="nav-link is-active" href="{{ route('about') }}">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7.5" r="3" stroke="currentColor"/><path d="M5 20c.7-3.1 3.4-5 7-5s6.3 1.9 7 5" stroke="currentColor" stroke-linecap="round"/></svg>
				About Us
			</a>
			<a class="nav-link" href="{{ route('contact') }}">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4z" stroke="currentColor"/><path d="M4 8l8 5 8-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
				Contact
			</a>
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
		<div class="hero-image" style="background-image: url('https://lh3.googleusercontent.com/gps-cs-s/AHVAwepdzIcqPd9G8e47PyoJ-YA3xOQ_OPuR5gtqFiqD9S83NgVt6ZfVKH9k8pcxzPOqpDs9C0iPUsc29Vdk1oGtaJ3S_muXfeqq26PITav-4z6QA8BFLXBP9AI3S1A4xe1g7XY2ATNT=s1360-w1360-h1020-rw');" aria-label="Environmental monitoring and mapping" role="img"></div>

		<section class="new-section" aria-label="About ENVIROTRACK and DENR CDO">
			<h2>About Us</h2>
			<div class="agency-badge">
				<img src="https://upload.wikimedia.org/wikipedia/commons/0/0f/Seal_of_the_Department_of_Environment_and_Natural_Resources.svg" alt="DENR Seal">
				<span>Department of Environment and Natural Resources</span>
			</div>
			<p>
				ENVIROTRACK supports the Department of Environment and Natural Resources (DENR) in advancing its mandate for
				environmental protection, conservation, and sustainable development through better community engagement and action.
			</p>

			<div class="insight-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
				<article class="insight-card">
					<h3>Vision</h3>
					<p>A nation enjoying and sustaining its natural resources and a clean and healthy environment.</p>
				</article>
				<article class="insight-card">
					<h3>Mission</h3>
					<p>To mobilize our citizenry in protecting, conserving, and managing the environment and natural resources for the present and future generations.</p>
				</article>
			</div>
		</section>

		<section class="section-lite" aria-label="Development goal and organizational outcomes">
			<h2>Development Goal</h2>
			<p>Human well-being, and environmental quality and sustainability ensured.</p>

			<h2>Organizational Outcomes</h2>
			<ul>
				<li>Promote human well-being and ensure environmental quality</li>
				<li>Sustainably-managed environment and natural resources</li>
				<li>Adaptive capacities of human communities and natural systems ensured</li>
			</ul>
		</section>

		<section class="section-lite" aria-label="ENR development principles">
			<h2>ENR Development Principles</h2>
			<ul>
				<li>Good governance</li>
				<li>Accountability, transparency, integrity, participatory and predictability</li>
				<li>Ease of doing business</li>
				<li>Social justice</li>
				<li>Equity and gross national happiness</li>
				<li>Social entrepreneurship</li>
				<li>Partnership with civil society</li>
				<li>Ecosystem integrity</li>
				<li>Sustainable consumption and production</li>
				<li>Polluters pay</li>
				<li>Payment for ecosystem services</li>
				<li>Rule of law</li>
				<li>Honoring global commitments</li>
			</ul>
		</section>
	</main>

	<footer class="footer">ENVIROTRACK: Smart Mapping</footer>
</body>
</html>