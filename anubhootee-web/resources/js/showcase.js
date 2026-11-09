/* Anubhootee: homepage chapter showcase
   Drag / wheel / keys, WebGL liquid transition, and an iPhone-style depth effect:
   each photo's subject (an AI cutout made by `php artisan anubhootee:depth`) is drawn
   on a second canvas ABOVE the big title, with the same distortion, transition and shading
   as the photo underneath, so it reads as one image with the title tucked behind the subject. */
(function () {
	var root = document.querySelector('.anu-showcase');
	if (!root) return;

	var data = JSON.parse(root.querySelector('.anu-sc-data').textContent);
	var CH = data.chapters, N = CH.length;
	var stage = root.querySelector('.anu-sc-stage');
	var layers = Array.prototype.slice.call(root.querySelectorAll('.anu-sc-slides'));
	var backLayer = root.querySelector('.anu-sc-slides--back') || layers[0];
	var dots = root.querySelectorAll('.anu-sc-progress button');
	var counter = root.querySelector('.anu-sc-counter');
	var credit = root.querySelector('.anu-sc-credit');
	var fallback = root.querySelector('.anu-sc-fallback');
	var fgFallback = root.querySelector('.anu-sc-fg-fallback');
	var loader = root.querySelector('.anu-sc-loader');
	var loadbar = root.querySelector('.anu-sc-loader__road');
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var pad = function (n) { return String(n).padStart(2, '0'); };
	var clamp = function (v, a, b) { return Math.max(a, Math.min(b, v)); };

	/* The same shading as .anu-sc-shade, so the cutout is darkened exactly like the photo under it */
	var SHADE_CSS = 'linear-gradient(0deg, rgba(11,9,7,.78) 0%, rgba(11,9,7,.38) 30%, rgba(11,9,7,0) 60%),'
		+ 'radial-gradient(ellipse 60% 40% at 50% 26%, rgba(11,9,7,.32) 0%, rgba(11,9,7,0) 100%),'
		+ 'linear-gradient(180deg, rgba(11,9,7,.45) 0%, rgba(11,9,7,0) 18%)';

	/* ---------- No-WebGL fallback layers ---------- */
	CH.forEach(function (c) {
		var im = document.createElement('img'); im.src = c.img; im.alt = ''; fallback.appendChild(im);
		var box = document.createElement('div');
		if (c.fg) {
			var fg = document.createElement('img'); fg.src = c.fg; fg.alt = ''; box.appendChild(fg);
			var shade = document.createElement('i');
			shade.style.cssText = 'background:' + SHADE_CSS + ';-webkit-mask:url("' + c.fg + '") center/cover no-repeat;mask:url("' + c.fg + '") center/cover no-repeat;';
			box.appendChild(shade);
		}
		if (fgFallback) fgFallback.appendChild(box);
	});

	/* ---------- Physics ---------- */
	var target = 0, current = 0, active = -1, velocity = 0;
	var gap = function () { return Math.min(window.innerHeight * 0.62, 560); };
	function setGap() { root.style.setProperty('--sc-gap', gap() + 'px'); }
	setGap();
	function goTo(i) { target = clamp(i, 0, N - 1); }
	Array.prototype.forEach.call(dots, function (b, i) { b.addEventListener('click', function () { goTo(i); }); });

	var dragging = false, startY = 0, startT = 0, lastY = 0, lastTime = 0, flick = 0;
	stage.addEventListener('pointerdown', function (e) {
		if (e.button > 0) return;
		dragging = true; startY = lastY = e.clientY; startT = target; lastTime = performance.now(); flick = 0;
		stage.classList.add('is-dragging');
	});
	window.addEventListener('pointermove', function (e) {
		if (!dragging) return;
		var now = performance.now();
		flick = (e.clientY - lastY) / Math.max(1, now - lastTime); lastY = e.clientY; lastTime = now;
		target = clamp(startT - (e.clientY - startY) / (gap() * 1.1), -0.25, N - 0.75);
	});
	function up() {
		if (!dragging) return;
		dragging = false; stage.classList.remove('is-dragging');
		target = clamp(Math.round(target - clamp(flick, -2, 2) * 0.45), 0, N - 1);
	}
	window.addEventListener('pointerup', up);
	window.addEventListener('pointercancel', up);

	var wheelAcc = 0, wheelLock = 0;
	window.addEventListener('wheel', function (e) {
		e.preventDefault();
		var now = performance.now();
		wheelAcc += e.deltaY;
		if (now > wheelLock && Math.abs(wheelAcc) > 40) { goTo(Math.round(target) + Math.sign(wheelAcc)); wheelAcc = 0; wheelLock = now + 650; }
		if (now > wheelLock) wheelAcc *= 0.9;
	}, { passive: false });
	window.addEventListener('keydown', function (e) {
		if (e.target && /input|textarea|select/i.test(e.target.tagName)) return;
		if (['ArrowDown', 'PageDown', ' '].indexOf(e.key) > -1) { e.preventDefault(); goTo(Math.round(target) + 1); }
		if (['ArrowUp', 'PageUp'].indexOf(e.key) > -1) { e.preventDefault(); goTo(Math.round(target) - 1); }
	});

	function setActive(i) {
		if (i === active) return;
		active = i;
		layers.forEach(function (layer) {
			for (var k = 0; k < N; k++) layer.children[k].classList.toggle('is-active', k === i);
		});
		for (var k = 0; k < N; k++) {
			dots[k].classList.toggle('is-on', k === i);
			fallback.children[k].classList.toggle('is-on', k === i);
			if (fgFallback) fgFallback.children[k].classList.toggle('is-on', k === i && depthOn[k]);
		}
		counter.innerHTML = '<b>' + pad(i + 1) + '</b> / ' + pad(N);
		credit.textContent = CH[i].credit ? data.photo + ': ' + CH[i].credit : '';
	}

	/* ---------- Depth on/off per chapter ----------
	   Like iOS, skip the effect when the subject would hide too much of the title. */
	var MAX_COVER = 0.42;
	var depthOn = CH.map(function () { return false; });
	var fgImages = [];
	function coverRect(iw, ih, W, H) { // where an object-fit: cover image lands on screen
		var s = Math.max(W / iw, H / ih); var w = iw * s, h = ih * s;
		return { x: (W - w) / 2, y: (H - h) / 2, w: w, h: h };
	}
	var probe = document.createElement('canvas');
	var probeCtx = probe.getContext('2d', { willReadFrequently: true });
	function measureDepth() {
		var W = window.innerWidth, H = window.innerHeight, S = 0.25;
		probe.width = Math.ceil(W * S); probe.height = Math.ceil(H * S);
		var layerBox = backLayer.getBoundingClientRect();
		var layerTop = parseFloat(getComputedStyle(backLayer).top) || 0;
		CH.forEach(function (c, i) {
			var im = fgImages[i];
			if (!im) { depthOn[i] = false; return; }
			var title = backLayer.children[i].querySelector('.anu-sc-title');
			// Title position when this chapter is at rest (no drag offset, no entrance animation)
			var tx = layerBox.left + title.offsetLeft, tw = title.offsetWidth;
			var ty = layerTop - i * gap() - gap() / 2 + title.offsetTop, th = title.offsetHeight;
			var r = coverRect(im.naturalWidth, im.naturalHeight, W, H);
			probeCtx.clearRect(0, 0, probe.width, probe.height);
			probeCtx.drawImage(im, r.x * S, r.y * S, r.w * S, r.h * S);
			var x0 = Math.max(0, Math.floor(tx * S)), y0 = Math.max(0, Math.floor(ty * S));
			var w = Math.max(1, Math.min(probe.width - x0, Math.ceil(tw * S))), h = Math.max(1, Math.min(probe.height - y0, Math.ceil(th * S)));
			var px = probeCtx.getImageData(x0, y0, w, h).data, covered = 0;
			for (var p = 3; p < px.length; p += 4) if (px[p] > 128) covered++;
			var share = covered / (w * h);
			depthOn[i] = share > 0.003 && share < MAX_COVER;
			backLayer.children[i].dataset.depth = depthOn[i] ? Math.round(share * 100) + '%' : 'off';
		});
		active = -1; // refresh fallback visibility
	}

	/* ---------- WebGL ---------- */
	var VS = 'attribute vec2 p; varying vec2 vUv; void main(){ vUv = p*0.5+0.5; gl_Position = vec4(p,0.,1.); }';
	var COMMON = [
		'precision highp float;',
		'varying vec2 vUv;',
		'uniform sampler2D t0, t1;',
		'uniform vec2 s0, s1, res;',
		'uniform float prog, vel, time, on0, on1;',
		'float h(vec2 p){ return fract(sin(dot(p, vec2(127.1,311.7)))*43758.5453); }',
		'float n(vec2 p){ vec2 i=floor(p), f=fract(p); f=f*f*(3.-2.*f); return mix(mix(h(i),h(i+vec2(1,0)),f.x), mix(h(i+vec2(0,1)),h(i+vec2(1,1)),f.x), f.y); }',
		'float fbm(vec2 p){ float v=0., a=.5; for(int i=0;i<4;i++){ v+=a*n(p); p*=2.03; a*=.5; } return v; }',
		'vec2 cover(vec2 uv, vec2 img){ float ra = res.x/res.y, ri = img.x/img.y; vec2 sc = ra > ri ? vec2(1., ri/ra) : vec2(ra/ri, 1.); return (uv-.5)*sc + .5; }',
		'vec4 samp(sampler2D t, vec2 uv, vec2 sz, float split){ vec2 c = cover(uv, sz); vec4 m = texture2D(t, c); return vec4(texture2D(t, c + vec2(split,0.)).r, m.g, texture2D(t, c - vec2(split,0.)).b, m.a); }',
		// Shared geometry: liquid mask m, wave and slow zoom, identical for photo and cutout
		'void geo(out vec2 base, out float m, out float nn){',
		'  vec2 uv = vUv; nn = fbm(uv*3.2 + vec2(0., time*.05));',
		'  float w = .18; float edge = (1.-uv.y)*.55 + nn*.45; float t = prog*(1.+2.*w) - w;',
		'  m = smoothstep(edge-w, edge+w, t);',
		'  float wave = sin(uv.y*9. + time*1.6)*.008*abs(vel); float zoom = 1. - .04*sin(time*.12);',
		'  base = (uv-.5)*zoom + .5 + vec2(wave, 0.);',
		'}',
		'vec3 grade(vec3 col){ col = mix(col, col*vec3(1.04,.99,.94), .4); float v = smoothstep(1.3, .4, length(vUv-.5)*1.4); return col*mix(.75, 1., v); }'
	].join('\n');
	var FS_PHOTO = COMMON + [
		'void main(){ vec2 base; float m, nn; geo(base, m, nn);',
		'  vec3 a = samp(t0, base + vec2(0., -m*.06*nn), s0, .0015*abs(vel)).rgb;',
		'  vec3 b = samp(t1, base + vec2(0., (1.-m)*.06*nn), s1, .0015*abs(vel)).rgb;',
		'  gl_FragColor = vec4(grade(mix(a, b, m)), 1.);',
		'}'
	].join('\n');
	var FS_SUBJECT = COMMON + [
		// Same layered gradients as .anu-sc-shade (screen space, y measured from the top)
		'vec3 shade(vec3 col){ float yt = 1.-vUv.y; vec3 ink = vec3(11.,9.,7.)/255.;',
		'  float b = yt > .4 ? (yt > .7 ? mix(.38,.78,(yt-.7)/.3) : mix(0.,.38,(yt-.4)/.3)) : 0.;',
		'  float r = .32*(1.-clamp(length(vec2((vUv.x-.5)/.6,(yt-.26)/.4)),0.,1.));',
		'  float t = yt < .18 ? mix(.45,0.,yt/.18) : 0.;',
		'  col = mix(col, ink, b); col = mix(col, ink, r); col = mix(col, ink, t); return col; }',
		'void main(){ vec2 base; float m, nn; geo(base, m, nn);',
		'  vec4 a = samp(t0, base + vec2(0., -m*.06*nn), s0, .0015*abs(vel)); a.a *= on0;',
		'  vec4 b = samp(t1, base + vec2(0., (1.-m)*.06*nn), s1, .0015*abs(vel)); b.a *= on1;',
		'  vec4 c = vec4(a.rgb*a.a, a.a)*(1.-m) + vec4(b.rgb*b.a, b.a)*m;', // premultiplied cross-fade
		'  vec3 rgb = c.a > 0.001 ? c.rgb / c.a : vec3(0.);',
		'  gl_FragColor = vec4(shade(grade(rgb))*c.a, c.a);',
		'}'
	].join('\n');

	function Renderer(canvas, fs, alpha) {
		var gl = null;
		try { gl = canvas.getContext('webgl', { antialias: false, premultipliedAlpha: true, alpha: alpha }); } catch (err) { gl = null; }
		if (!gl) throw new Error('no webgl');
		function sh(type, src) {
			var s = gl.createShader(type); gl.shaderSource(s, src); gl.compileShader(s);
			if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) throw new Error(gl.getShaderInfoLog(s));
			return s;
		}
		var prog = gl.createProgram();
		gl.attachShader(prog, sh(gl.VERTEX_SHADER, VS)); gl.attachShader(prog, sh(gl.FRAGMENT_SHADER, fs));
		gl.linkProgram(prog); gl.useProgram(prog);
		var buf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buf);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW);
		var loc = gl.getAttribLocation(prog, 'p'); gl.enableVertexAttribArray(loc); gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
		var uni = {};
		['t0', 't1', 's0', 's1', 'res', 'prog', 'vel', 'time', 'on0', 'on1'].forEach(function (k) { uni[k] = gl.getUniformLocation(prog, k); });
		gl.uniform1i(uni.t0, 0); gl.uniform1i(uni.t1, 1);
		gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
		var textures = [], sizes = [];
		this.setImages = function (list, fmt) {
			list.forEach(function (im, i) {
				var t = gl.createTexture(); gl.bindTexture(gl.TEXTURE_2D, t);
				if (im) gl.texImage2D(gl.TEXTURE_2D, 0, fmt, fmt, gl.UNSIGNED_BYTE, im);
				else gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1, 1, 0, gl.RGBA, gl.UNSIGNED_BYTE, new Uint8Array(4)); // empty
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
				textures[i] = t; sizes[i] = im ? [im.naturalWidth, im.naturalHeight] : [1, 1];
			});
		};
		this.resize = function () {
			var dpr = Math.min(window.devicePixelRatio || 1, 2);
			canvas.width = window.innerWidth * dpr; canvas.height = window.innerHeight * dpr;
			gl.viewport(0, 0, canvas.width, canvas.height);
			gl.uniform2f(uni.res, canvas.width, canvas.height);
		};
		this.draw = function (i0, i1, p, vel, time, on0, on1, sizeOf) {
			gl.clearColor(0, 0, 0, 0); gl.clear(gl.COLOR_BUFFER_BIT);
			gl.activeTexture(gl.TEXTURE0); gl.bindTexture(gl.TEXTURE_2D, textures[i0]);
			gl.activeTexture(gl.TEXTURE1); gl.bindTexture(gl.TEXTURE_2D, textures[i1]);
			var a = sizeOf(i0), b = sizeOf(i1);
			gl.uniform2f(uni.s0, a[0], a[1]); gl.uniform2f(uni.s1, b[0], b[1]);
			gl.uniform1f(uni.prog, p); gl.uniform1f(uni.vel, vel); gl.uniform1f(uni.time, time);
			gl.uniform1f(uni.on0, on0); gl.uniform1f(uni.on1, on1);
			gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
		};
		this.sizes = sizes;
		this.resize();
	}

	/* ---------- Load & loop ---------- */
	var total = N + CH.filter(function (c) { return c.fg; }).length, loaded = 0;
	function load(src) {
		return new Promise(function (res) {
			if (!src) { res(null); return; }
			var im = new Image();
			im.crossOrigin = 'anonymous';
			im.onload = function () { loaded++; loadbar.style.width = (loaded / total * 100) + '%'; res(im); };
			im.onerror = function () { loaded++; res(null); };
			im.src = src;
		});
	}
	var photosP = Promise.all(CH.map(function (c) { return load(c.img); }));
	var cutoutsP = Promise.all(CH.map(function (c) { return load(c.fg); }));

	Promise.all([photosP, cutoutsP, document.fonts ? document.fonts.ready : null]).then(function (res) {
		var photos = res[0]; fgImages = res[1];
		var photoGL = null, subjectGL = null;
		if (photos.every(Boolean)) {
			try {
				photoGL = new Renderer(root.querySelector('.anu-sc-gl'), FS_PHOTO, false);
				photoGL.setImages(photos, WebGLRenderingContext.RGB);
				var fgCanvas = root.querySelector('.anu-sc-fg-gl');
				if (fgCanvas && fgImages.some(Boolean)) {
					subjectGL = new Renderer(fgCanvas, FS_SUBJECT, true);
					subjectGL.setImages(fgImages, WebGLRenderingContext.RGBA);
				}
			} catch (err) { photoGL = null; subjectGL = null; }
		}
		if (!photoGL) root.classList.add('no-webgl');

		measureDepth();
		window.addEventListener('resize', function () {
			setGap();
			if (photoGL) photoGL.resize();
			if (subjectGL) subjectGL.resize();
			clearTimeout(window.__anuDepthT);
			window.__anuDepthT = setTimeout(measureDepth, 150);
		});

		setActive(0);
		setTimeout(function () { loader.classList.add('is-done'); }, 350);
		var photoSize = function (i) { return photoGL.sizes[i]; }; // the cutout uses the photo's size for "cover"
		var last = 0;
		function loop(now) {
			var raw = last ? now - last : 16, dt = Math.min(64, raw); last = now;
			var prev = current, k = dragging ? 0.35 : 0.085;
			current += (target - current) * (1 - Math.pow(1 - k, dt / 16.67));
			// Speed in chapters per second, measured on real elapsed time (slow frames don't exaggerate it)
			velocity += ((current - prev) * 1000 / Math.max(16, raw) - velocity) * 0.2;
			var y = 'translateY(' + (-current * gap() - gap() / 2) + 'px)';
			layers.forEach(function (l) { l.style.transform = y; });
			setActive(clamp(Math.round(current), 0, N - 1));
			if (photoGL) {
				var c = clamp(current, 0, N - 1), i0 = Math.floor(c), p = c - i0, i1 = Math.min(i0 + 1, N - 1);
				if (reduced) p = p > 0.5 ? 1 : 0;
				var vel = reduced ? 0 : clamp(velocity, -3, 3), time = reduced ? 0 : now / 1000;
				photoGL.draw(i0, i1, p, vel, time, 1, 1, photoSize);
				if (subjectGL) subjectGL.draw(i0, i1, p, vel, time, depthOn[i0] ? 1 : 0, depthOn[i1] ? 1 : 0, photoSize);
			}
			requestAnimationFrame(loop);
		}
		requestAnimationFrame(loop);
	});
})();
