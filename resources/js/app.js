import './bootstrap';
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { GUI } from 'lil-gui';
import { gsap } from 'gsap';
import worldSvg from '@svg-maps/world/world.svg?raw';

if (document.body.dataset.page === 'environmental-monitoring') {
	const revealItems = document.querySelectorAll('.reveal');
	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('show');
				}
			});
		},
		{ threshold: 0.15 }
	);

	revealItems.forEach((item) => observer.observe(item));

	const counters = document.querySelectorAll('[data-counter]');
	counters.forEach((counter) => {
		const target = Number(counter.dataset.counter) || 0;
		const duration = 1000;
		const start = performance.now();

		const tick = (now) => {
			const progress = Math.min((now - start) / duration, 1);
			counter.textContent = Math.floor(progress * target).toLocaleString();

			if (progress < 1) {
				requestAnimationFrame(tick);
			}
		};

		requestAnimationFrame(tick);
	});
}

if (document.body.dataset.page === 'interactive-map') {
	const points = document.querySelectorAll('.map-point');

	const closeAll = () => {
		points.forEach((point) => point.classList.remove('is-active'));
	};

	points.forEach((point) => {
		point.addEventListener('click', (event) => {
			const alreadyOpen = point.classList.contains('is-active');
			closeAll();

			if (!alreadyOpen) {
				point.classList.add('is-active');
			}

			event.stopPropagation();
		});
	});

	document.addEventListener('click', (event) => {
		if (!(event.target instanceof Element)) {
			return;
		}

		if (!event.target.closest('.map-point')) {
			closeAll();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeAll();
		}
	});
}

if (document.body.dataset.page === 'globe-map') {
	const containerEl = document.querySelector('.globe-wrapper');
	const canvasEl = document.getElementById('globe-3d');
	const svgMapDomEl = document.getElementById('map');
	const svgCountryDomEl = document.getElementById('country');
	const countryNameEl = document.querySelector('.info span');
	const topControlsEl = document.getElementById('top-controls');

	if (!containerEl || !canvasEl || !svgMapDomEl || !svgCountryDomEl || !countryNameEl) {
		throw new Error('Globe map elements are missing.');
	}

	const parser = new DOMParser();
	const worldSvgDoc = parser.parseFromString(worldSvg, 'image/svg+xml');
	const sourceSvg = worldSvgDoc.documentElement;
	const sourcePaths = Array.from(sourceSvg.querySelectorAll('path'));

	for (const sourcePath of sourcePaths) {
		const cloned = sourcePath.cloneNode(true);
		cloned.setAttribute('data-name', sourcePath.getAttribute('aria-label') || sourcePath.getAttribute('id') || 'Unknown');
		svgMapDomEl.appendChild(cloned);
	}

	const svgCountries = Array.from(svgMapDomEl.querySelectorAll('path'));

	let renderer;
	let scene;
	let camera;
	let rayCaster;
	let pointer;
	let controls;
	let globeGroup;
	let globeColorMesh;
	let globeStrokesMesh;
	let globeSelectionOuterMesh;

	const viewBoxValue = (sourceSvg.getAttribute('viewBox') || '0 0 1010 666').split(/\s+/).map(Number);
	const svgViewBox = [viewBoxValue[2], viewBoxValue[3]];
	const offsetY = -0.1;

	const params = {
		strokeColor: '#111111',
		defaultColor: '#9a9591',
		hoverColor: '#00C9A2',
		fogColor: '#e4e5e6',
		fogDistance: 2.6,
		strokeWidth: 1.2,
		hiResScalingFactor: 2,
		lowResScalingFactor: 0.7,
	};

	let hoveredCountryIdx = Math.max(0, svgCountries.findIndex((path) => path.getAttribute('data-name') === 'Philippines'));
	let isTouchScreen = false;
	let isHoverable = true;

	const textureLoader = new THREE.TextureLoader();
	const bBoxes = [];
	const dataUris = [];

	initScene();
	createControls();

	window.addEventListener('resize', updateSize);

	containerEl.addEventListener('touchstart', () => {
		isTouchScreen = true;
	});
	containerEl.addEventListener('mousemove', (event) => {
		updateMousePosition(event.clientX, event.clientY);
	});
	containerEl.addEventListener('click', (event) => {
		updateMousePosition(event.clientX, event.clientY);
	});

	function updateMousePosition(x, y) {
		pointer.x = ((x - containerEl.offsetLeft) / containerEl.offsetWidth) * 2 - 1;
		pointer.y = -((y - containerEl.offsetTop) / containerEl.offsetHeight) * 2 + 1;
	}

	function initScene() {
		renderer = new THREE.WebGLRenderer({ canvas: canvasEl, alpha: true, antialias: true });
		renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

		scene = new THREE.Scene();
		scene.fog = new THREE.Fog(params.fogColor, 0, params.fogDistance);

		camera = new THREE.OrthographicCamera(-1.2, 1.2, 1.2, -1.2, 0, 3);
		camera.position.z = 1.3;

		globeGroup = new THREE.Group();
		scene.add(globeGroup);

		rayCaster = new THREE.Raycaster();
		rayCaster.far = 1.15;
		pointer = new THREE.Vector2(-1, -1);

		createOrbitControls();
		createGlobe();
		prepareHiResTextures();
		prepareLowResTextures();
		updateSize();

		gsap.ticker.add(render);
	}

	function createOrbitControls() {
		controls = new OrbitControls(camera, canvasEl);
		controls.enablePan = false;
		controls.enableZoom = false;
		controls.enableDamping = true;
		controls.minPolarAngle = 0.46 * Math.PI;
		controls.maxPolarAngle = 0.46 * Math.PI;
		controls.autoRotate = true;
		controls.autoRotateSpeed *= 1.2;

		controls.addEventListener('start', () => {
			isHoverable = false;
			pointer = new THREE.Vector2(-1, -1);
			gsap.to(globeGroup.scale, {
				duration: 0.3,
				x: 0.9,
				y: 0.9,
				z: 0.9,
				ease: 'power1.inOut',
			});
		});

		controls.addEventListener('end', () => {
			gsap.to(globeGroup.scale, {
				duration: 0.6,
				x: 1,
				y: 1,
				z: 1,
				ease: 'back.out(1.7)',
				onComplete: () => {
					isHoverable = true;
				},
			});
		});
	}

	function createGlobe() {
		const globeGeometry = new THREE.IcosahedronGeometry(1, 20);

		const globeColorMaterial = new THREE.MeshBasicMaterial({
			transparent: true,
			alphaTest: true,
			side: THREE.DoubleSide,
		});
		const globeStrokeMaterial = new THREE.MeshBasicMaterial({
			transparent: true,
			depthTest: false,
		});
		const outerSelectionColorMaterial = new THREE.MeshBasicMaterial({
			transparent: true,
			side: THREE.DoubleSide,
		});

		globeColorMesh = new THREE.Mesh(globeGeometry, globeColorMaterial);
		globeStrokesMesh = new THREE.Mesh(globeGeometry, globeStrokeMaterial);
		globeSelectionOuterMesh = new THREE.Mesh(globeGeometry, outerSelectionColorMaterial);
		globeStrokesMesh.renderOrder = 2;

		globeGroup.add(globeStrokesMesh, globeSelectionOuterMesh, globeColorMesh);
	}

	function setMapTexture(material, uri) {
		textureLoader.load(uri, (texture) => {
			texture.repeat.set(1, 1);
			material.map = texture;
			material.needsUpdate = true;
		});
	}

	function prepareHiResTextures() {
		let svgData;
		gsap.set(svgMapDomEl, {
			attr: {
				viewBox: `0 ${offsetY * svgViewBox[1]} ${svgViewBox[0]} ${svgViewBox[1]}`,
				'stroke-width': params.strokeWidth,
				stroke: params.strokeColor,
				fill: params.defaultColor,
				width: svgViewBox[0] * params.hiResScalingFactor,
				height: svgViewBox[1] * params.hiResScalingFactor,
			},
		});

		svgData = new XMLSerializer().serializeToString(svgMapDomEl);
		setMapTexture(globeColorMesh.material, `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svgData)}`);

		gsap.set(svgMapDomEl, {
			attr: {
				fill: 'none',
				stroke: params.strokeColor,
			},
		});

		svgData = new XMLSerializer().serializeToString(svgMapDomEl);
		setMapTexture(globeStrokesMesh.material, `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svgData)}`);
		countryNameEl.textContent = svgCountries[hoveredCountryIdx].getAttribute('data-name') || 'Country';
	}

	function prepareLowResTextures() {
		gsap.set(svgCountryDomEl, {
			attr: {
				viewBox: `0 ${offsetY * svgViewBox[1]} ${svgViewBox[0]} ${svgViewBox[1]}`,
				'stroke-width': params.strokeWidth,
				stroke: params.strokeColor,
				fill: params.hoverColor,
				width: svgViewBox[0] * params.lowResScalingFactor,
				height: svgViewBox[1] * params.lowResScalingFactor,
			},
		});

		svgCountries.forEach((path, idx) => {
			bBoxes[idx] = path.getBBox();
		});

		svgCountries.forEach((path, idx) => {
			svgCountryDomEl.innerHTML = '';
			svgCountryDomEl.appendChild(path.cloneNode(true));
			const svgData = new XMLSerializer().serializeToString(svgCountryDomEl);
			dataUris[idx] = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svgData)}`;
		});

		setMapTexture(globeSelectionOuterMesh.material, dataUris[hoveredCountryIdx]);
	}

	function updateMap(uv = { x: 0, y: 0 }) {
		const pointObj = svgMapDomEl.createSVGPoint();
		pointObj.x = uv.x * svgViewBox[0];
		pointObj.y = (1 + offsetY - uv.y) * svgViewBox[1];

		for (let i = 0; i < svgCountries.length; i += 1) {
			const boundingBox = bBoxes[i];
			if (
				pointObj.x > boundingBox.x &&
				pointObj.x < boundingBox.x + boundingBox.width &&
				pointObj.y > boundingBox.y &&
				pointObj.y < boundingBox.y + boundingBox.height
			) {
				const isHovering = typeof svgCountries[i].isPointInFill === 'function'
					? svgCountries[i].isPointInFill(pointObj)
					: false;
				if (isHovering && i !== hoveredCountryIdx) {
					hoveredCountryIdx = i;
					setMapTexture(globeSelectionOuterMesh.material, dataUris[hoveredCountryIdx]);
					countryNameEl.textContent = svgCountries[hoveredCountryIdx].getAttribute('data-name') || 'Country';
					break;
				}
			}
		}
	}

	function updateTopControlActiveState(countryName) {
		if (!topControlsEl) {
			return;
		}

		topControlsEl.querySelectorAll('button[data-country]').forEach((button) => {
			button.classList.toggle('is-active', button.getAttribute('data-country') === countryName);
		});
	}

	function focusCountryByName(countryName) {
		const idx = svgCountries.findIndex((path) => path.getAttribute('data-name') === countryName);
		if (idx < 0) {
			return;
		}

		hoveredCountryIdx = idx;
		setMapTexture(globeSelectionOuterMesh.material, dataUris[hoveredCountryIdx]);
		countryNameEl.textContent = svgCountries[hoveredCountryIdx].getAttribute('data-name') || 'Country';
		updateTopControlActiveState(countryName);
	}

	function render() {
		controls.update();

		if (isHoverable) {
			rayCaster.setFromCamera(pointer, camera);
			const intersects = rayCaster.intersectObject(globeStrokesMesh);
			if (intersects.length > 0 && intersects[0].uv) {
				updateMap(intersects[0].uv);
			}
		}

		if (isTouchScreen && isHoverable) {
			isHoverable = false;
		}

		renderer.render(scene, camera);
	}

	function updateSize() {
		const side = Math.min(640, Math.min(window.innerWidth, window.innerHeight) - 50);
		containerEl.style.width = `${side}px`;
		containerEl.style.height = `${side}px`;
		renderer.setSize(side, side);
	}

	function createControls() {
		const gui = new GUI();
		gui.close();

		gui.addColor(params, 'strokeColor').onChange(prepareHiResTextures).name('stroke');
		gui.addColor(params, 'defaultColor').onChange(prepareHiResTextures).name('color');
		gui.addColor(params, 'hoverColor').onChange(prepareLowResTextures).name('highlight');
		gui
			.addColor(params, 'fogColor')
			.onChange(() => {
				scene.fog = new THREE.Fog(params.fogColor, 0, params.fogDistance);
			})
			.name('fog');
		gui
			.add(params, 'fogDistance', 1, 4)
			.onChange(() => {
				scene.fog = new THREE.Fog(params.fogColor, 0, params.fogDistance);
			})
			.name('fog distance');
	}

	if (topControlsEl) {
		topControlsEl.addEventListener('click', (event) => {
			const target = event.target;
			if (!(target instanceof HTMLButtonElement)) {
				return;
			}

			const country = target.getAttribute('data-country');
			if (country) {
				focusCountryByName(country);
			}
		});

		focusCountryByName('Philippines');
	}
}
