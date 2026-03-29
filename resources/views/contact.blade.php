<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Contact Us | {{ config('app.name') }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
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
			z-index: 2000;
			backdrop-filter: blur(6px);
		}

		/* Keep Leaflet map layers below the fixed navbar */
		.leaflet-pane,
		.leaflet-control,
		.leaflet-top,
		.leaflet-bottom {
			z-index: 500;
		}

		.brand {
			font-weight: 900;
			letter-spacing: 0.04em;
			text-transform: uppercase;
			color: #0b6d5a;
			font-size: clamp(16px, 1.8vw, 28px);
			white-space: normal;
			line-height: 1.35;
			max-width: min(780px, 56vw);
			display: inline-flex;
			align-items: center;
			gap: 10px;
		}

		.brand span {
			display: inline-block;
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

		.hero {
			width: min(1100px, 92vw);
			margin: 20px auto;
			background: linear-gradient(120deg, #e8fff8 0%, #ffffff 62%);
			border: 1px solid #dcefe9;
			border-radius: 14px;
			padding: clamp(20px, 3.2vw, 34px);
		}

		.hero h1 {
			margin: 0 0 10px;
			font-size: clamp(24px, 3vw, 40px);
			line-height: 1.15;
			letter-spacing: 0.01em;
			text-transform: uppercase;
			color: #124f44;
		}

		.hero p {
			margin: 0;
			max-width: 850px;
			line-height: 1.65;
			color: #355a52;
		}

		.contact-grid {
			width: min(1100px, 92vw);
			margin: 0 auto 20px;
			display: grid;
			gap: 12px;
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}

		.contact-card {
			background: #ffffff;
			border: 1px solid #dddddd;
			border-radius: 12px;
			padding: 18px;
		}

		.contact-card h2 {
			margin: 0 0 10px;
			font-size: 14px;
			letter-spacing: 0.05em;
			text-transform: uppercase;
			color: #0e5f63;
		}

		.contact-card p,
		.contact-card li {
			margin: 0;
			line-height: 1.6;
			color: #3f3f3f;
		}

		.contact-card ul {
			margin: 0;
			padding-left: 18px;
		}

		.card-muted {
			font-size: 12px;
			color: #5e6f6a;
			margin-top: 8px;
		}

		.hours-grid {
			display: grid;
			grid-template-columns: 1.1fr 1fr;
			gap: 8px 14px;
			margin-top: 6px;
		}

		.hours-day {
			font-weight: 700;
			color: #2f4f47;
		}

		.hours-time {
			color: #3f3f3f;
		}

		.contact-link {
			display: inline-block;
			margin-top: 8px;
			color: #0d6656;
			text-decoration: none;
			font-weight: 700;
		}

		.contact-link:hover {
			text-decoration: underline;
		}

		.coordinates {
			display: inline-block;
			margin-top: 10px;
			padding: 6px 10px;
			border-radius: 8px;
			background: #f2fbf7;
			border: 1px solid #d0ece3;
			color: #245e52;
			font-size: 12px;
			font-weight: 700;
			letter-spacing: 0.02em;
		}

		.notice {
			width: min(1100px, 92vw);
			margin: 0 auto 20px;
			background: #ffffff;
			border: 1px dashed #b4d8cf;
			border-radius: 12px;
			padding: 16px 18px;
			color: #385850;
			line-height: 1.65;
		}

		.map-section {
			width: min(1100px, 92vw);
			margin: 0 auto 20px;
			background: #ffffff;
			border: 1px solid #dddddd;
			border-radius: 12px;
			padding: 18px;
		}

		.map-section h2 {
			margin: 0 0 10px;
			font-size: 14px;
			letter-spacing: 0.05em;
			text-transform: uppercase;
			color: #0e5f63;
		}

		.map-controls {
			display: flex;
			gap: 8px;
			margin-bottom: 12px;
		}

		.map-button {
			border: 1px solid #bcd6cf;
			background: #effaf6;
			color: #176151;
			padding: 8px 12px;
			border-radius: 999px;
			font-size: 12px;
			letter-spacing: 0.04em;
			text-transform: uppercase;
			font-weight: 700;
			cursor: pointer;
		}

		.map-button:hover {
			background: #e1f6f0;
		}

		.map-button.is-active {
			background: #0e5f63;
			border-color: #0e5f63;
			color: #ffffff;
		}

		.map-canvas {
			height: 340px;
			border-radius: 10px;
			border: 1px solid #d8d8d8;
			overflow: hidden;
		}

		.coords {
			padding: 12px 14px;
			font-size: 13px;
			color: #325f55;
			border: 1px solid #e2f1ed;
			border-top: none;
			border-radius: 0 0 10px 10px;
			background: #f7fffc;
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

			.contact-grid {
				grid-template-columns: 1fr;
			}
		}
	</style>
</head>
<body>
	<header class="navbar">
		<div class="brand">
			<div class="brand-logo"><img src="https://lh3.googleusercontent.com/gps-cs-s/AHVAwepdPH0a06dQd322E7KRfJZRGgDeBNF7JRK6QYOMAEw3b0Bzzb1LGB6rNykPnsCHVhhZ3s7tW0AKhQB8pAXItt7Jw6EHbfAMr-7xXZTdYtcWp0FzEB9tK27iT8v7BrBbqy6bkU1uaw=s1360-w1360-h1020-rw" alt="Logo"></div>
			<span>ENVIROTRACK: Smart Mapping</span>
		</div>
		<nav class="main-nav" aria-label="Main navigation">
			<a class="nav-link" href="{{ url('/') }}#home">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 9.5V20h13V9.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
				Home
			</a>
			<a class="nav-link" href="{{ route('about') }}">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="7.5" r="3" stroke="currentColor"/><path d="M5 20c.7-3.1 3.4-5 7-5s6.3 1.9 7 5" stroke="currentColor" stroke-linecap="round"/></svg>
				About Us
			</a>
			<a class="nav-link is-active" href="{{ route('contact') }}">
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
		<section class="hero" aria-label="Contact introduction">
			<h1>Contact Us</h1>
			<p>
				Need help with a report, account access, or map issue? Reach the ENVIROTRACK support and DENR CDO coordination team
				through the channels below. Include your report ID, location, and a short issue summary so we can assist you faster.
			</p>
		</section>

		<section class="map-section" aria-label="Office map location">
			<h2>Office Map</h2>
			<div class="map-controls" role="group" aria-label="Map layer mode">
				<button id="map-normal" class="map-button" type="button">Normal</button>
				<button id="map-satellite" class="map-button is-active" type="button">Satellite</button>
			</div>
			<div id="contact-map" class="map-canvas" role="img" aria-label="Map showing DENR CDO office location"></div>
			<div id="coords" class="coords">Latitude: 8.497171 | Longitude: 124.659870</div>
		</section>

		<section class="contact-grid" aria-label="Office location and response details" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
			<article class="contact-card">
				<h2>Main Office</h2>
				<p>
					FMW5+WW7, Julio Pacana Street,<br>
					Cagayan De Oro City, 9000<br>
					Misamis Oriental
				</p>
				<div id="office-coordinates" class="coordinates">Latitude: 8.497171 | Longitude: 124.659870</div>
				<a class="contact-link" href="https://maps.google.com/?q=FMW5%2BWW7%20Julio%20Pacana%20Street%20Cagayan%20de%20Oro" target="_blank" rel="noopener noreferrer">Open in Google Maps</a>
			</article>

			<article class="contact-card">
				<h2>Official Directory</h2>
				<p>Department of Environment and Natural Resources - Region X</p>
				<a class="contact-link" href="https://r10.denr.gov.ph/" target="_blank" rel="noopener noreferrer">https://r10.denr.gov.ph/</a>
				<ul>
					<li>Phone: 0888568780</li>
					<li>Status: Closed, opens 8 AM Monday</li>
				</ul>
				<p class="card-muted">Detailed weekly schedule is shown in the card below.</p>
			</article>
		</section>

		<section class="contact-grid" aria-label="Office weekly operating hours" style="grid-template-columns: 1fr;">
			<article class="contact-card">
				<h2>Weekly Operating Hours</h2>
				<div class="hours-grid">
					<div class="hours-day">Sunday</div>
					<div class="hours-time">Closed</div>

					<div class="hours-day">Monday</div>
					<div class="hours-time">8 AM - 5 PM</div>

					<div class="hours-day">Tuesday</div>
					<div class="hours-time">8 AM - 5 PM</div>

					<div class="hours-day">Wednesday (Holy Wednesday)</div>
					<div class="hours-time">8 AM - 5 PM (hours might differ)</div>

					<div class="hours-day">Thursday (Maundy Thursday)</div>
					<div class="hours-time">8 AM - 5 PM (hours might differ)</div>

					<div class="hours-day">Friday (Good Friday)</div>
					<div class="hours-time">8 AM - 5 PM (hours might differ)</div>

					<div class="hours-day">Saturday (Holy Saturday)</div>
					<div class="hours-time">Closed (hours might differ)</div>
				</div>
			</article>
		</section>

		<section class="notice" aria-label="Support reminder">
			For faster assistance, include your full name, contact number, exact location, and at least one photo reference when reporting an environmental incident.
		</section>
	</main>

	<footer class="footer">ENVIROTRACK: Smart Mapping</footer>
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			if (typeof L === 'undefined') {
				return;
			}

			var officeCoordinates = [8.497171, 124.659870];
			var coordsEl = document.getElementById('coords');
			var officeCoordinatesEl = document.getElementById('office-coordinates');
			var map = L.map('contact-map', {
				scrollWheelZoom: false
			}).setView(officeCoordinates, 14);

			var normalLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19,
				attribution: '&copy; OpenStreetMap contributors'
			});

			var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
				maxZoom: 19,
				attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community'
			});

			satelliteLayer.addTo(map);

			var normalButton = document.getElementById('map-normal');
			var satelliteButton = document.getElementById('map-satellite');

			function setActiveButton(mode) {
				if (!normalButton || !satelliteButton) {
					return;
				}

				normalButton.classList.toggle('is-active', mode === 'normal');
				satelliteButton.classList.toggle('is-active', mode === 'satellite');
			}

			if (normalButton) {
				normalButton.addEventListener('click', function () {
					if (map.hasLayer(satelliteLayer)) {
						map.removeLayer(satelliteLayer);
					}
					if (!map.hasLayer(normalLayer)) {
						normalLayer.addTo(map);
					}
					setActiveButton('normal');
				});
			}

			if (satelliteButton) {
				satelliteButton.addEventListener('click', function () {
					if (map.hasLayer(normalLayer)) {
						map.removeLayer(normalLayer);
					}
					if (!map.hasLayer(satelliteLayer)) {
						satelliteLayer.addTo(map);
					}
					setActiveButton('satellite');
				});
			}

			function updateCoordinateIndicators(lat, lng, isManual) {
				var latText = lat.toFixed(6);
				var lngText = lng.toFixed(6);
				if (coordsEl) {
					coordsEl.textContent = isManual
						? 'Pinned location: ' + latText + ', ' + lngText + ' (manual)'
						: 'Latitude: ' + latText + ' | Longitude: ' + lngText;
				}
				if (officeCoordinatesEl) {
					officeCoordinatesEl.textContent = 'Latitude: ' + latText + ' | Longitude: ' + lngText;
				}
			}

			var officeMarker = L.marker(officeCoordinates, {
				draggable: true
			}).addTo(map);

			officeMarker.bindPopup('FMW5+WW7, Julio Pacana Street, Cagayan De Oro City, 9000 Misamis Oriental<br>Lat: 8.497171, Lng: 124.659870').openPopup();

			officeMarker.on('dragend', function (event) {
				var marker = event.target;
				var latLng = marker.getLatLng();
				var lat = latLng.lat;
				var lng = latLng.lng;

				updateCoordinateIndicators(lat, lng, true);
				marker.bindPopup('FMW5+WW7, Julio Pacana Street, Cagayan De Oro City, 9000 Misamis Oriental<br>Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6));
			});

			updateCoordinateIndicators(officeCoordinates[0], officeCoordinates[1], false);
		});
	</script>
</body>
</html>
