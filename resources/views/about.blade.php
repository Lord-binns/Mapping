<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>About Us | {{ config('app.name') }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<style>
		body {
			margin: 0;
			min-height: 100vh;
			font-family: sans-serif;
			background: linear-gradient(160deg, #f2fffb 0%, #e7fbf5 52%, #ffffff 100%);
			color: #1b1b1b;
			padding: 24px;
		}

		.about-shell {
			width: min(980px, 96vw);
			margin: 0 auto;
			background: #ffffff;
			border: 1px solid #d9ebe6;
			border-radius: 14px;
			box-shadow: 0 14px 28px rgba(0, 121, 101, 0.1);
			padding: 24px;
		}

		.about-shell h1 {
			margin: 0 0 12px;
			color: #0b6d5a;
			font-size: clamp(22px, 2.4vw, 32px);
			line-height: 1.25;
		}

		.about-shell h2 {
			margin: 18px 0 8px;
			font-size: 18px;
			color: #114f43;
		}

		.about-shell p {
			margin: 0 0 12px;
			line-height: 1.65;
			color: #35524c;
		}

		.about-shell ul {
			margin: 0;
			padding-left: 20px;
			color: #35524c;
			line-height: 1.65;
		}

		.about-links {
			margin-top: 22px;
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
		}

		.about-links a {
			text-decoration: none;
			border: 1px solid #8edac9;
			color: #0b6d5a;
			background: #ffffff;
			border-radius: 999px;
			padding: 9px 14px;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 0.03em;
		}

		.about-links a:hover {
			border-color: #00c9a2;
			background: #ecfffb;
		}
	</style>
</head>
<body>
	<section class="about-shell" aria-label="About ENVIROTRACK and DENR CDO">
		<h1>About Us</h1>
		<p>
			<strong>{{ config('app.name') }}</strong> is a digital environmental reporting and mapping platform built to support
			the field and monitoring initiatives of <strong>DENR CDO (Department of Environment and Natural Resources - Cagayan de Oro)</strong>.
			It enables community members and responders to submit geotagged incident reports with photos and notes,
			helping turn scattered complaints into organized, location-based evidence.
		</p>

		<h2>Our Mission</h2>
		<p>
			We aim to strengthen environmental governance by combining agile workflows with real-time spatial data.
			Through faster reporting, verification, and hotspot analysis, DENR CDO and partner stakeholders can prioritize
			response actions, track recurring violations, and improve transparency in local environmental protection efforts.
		</p>

		<h2>What ENVIROTRACK Supports</h2>
		<ul>
			<li>Real-time pinning of pollution incidents and environmental violations</li>
			<li>Photo-backed submissions for stronger verification and documentation</li>
			<li>Status tracking from pending to verified and resolved reports</li>
			<li>Map layers and heatmaps for identifying high-risk and recurring areas</li>
			<li>Data-informed planning for cleanup drives, inspections, and community action</li>
		</ul>

		<h2>Community and Agency Collaboration</h2>
		<p>
			ENVIROTRACK is designed to bridge citizens, barangays, and DENR CDO teams through one shared reporting space.
			By making environmental data visible and actionable, the system supports timely interventions and long-term
			ecosystem protection for Cagayan de Oro and nearby communities.
		</p>

		<div class="about-links">
			<a href="{{ url('/') }}">Back Home</a>
			<a href="{{ route('login') }}">Login</a>
			<a href="{{ route('register') }}">Register</a>
		</div>
	</section>
</body>
</html>