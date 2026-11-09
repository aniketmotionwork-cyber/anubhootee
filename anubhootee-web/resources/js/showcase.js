/* Anubhootee: homepage chapter showcase
 *
 * Transition between chapters:
 *  - Photo: a "column wipe". The new photo rises into place in six staggered vertical
 *    slices, led by a thin turmeric line, while the old photo eases back and darkens (WebGL).
 *  - Depth effect: the photo's subject (AI cutout, see `php artisan anubhootee:depth`) is drawn on
 *    a second canvas above the big title, with exactly the same wipe, so it stays in front of the title.
 *  - Text: animated with Motion (Framer Motion's engine). Letters of the old title lift away with blur;
 *    the new title's letters spring up one by one with a 3D tilt; label, Marathi word and link follow.
 *  - Drag / wheel / arrow keys / the numbered bar all move between chapters. Dragging scrubs the wipe.
 */
import { animate, stagger } from 'motion';

(function () {
	var root = document.querySelector('.anu-showcase');
	if (!root) return;

	var data = JSON.parse(root.querySelector('.anu-sc-data').textContent);
	var CH = data.chapters, N = CH.length;
	var stage = root.querySelector('.anu-sc-stage');
	var back = root.querySelector('.anu-sc-slides--back');
	var front = root.querySelector('.anu-sc-slides--front');
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
	var EASE_OUT = [0.22, 1, 0.36, 1], EASE_IN = [0.55, 0, 0.75, 0.2], EASE_IN_OUT = [0.76, 0, 0.24, 1];

	/* Same shading as .anu-sc-shade, for the no-WebGL cutout fallback */
	var SHADE_CSS = 'linear-gradient(0deg, rgba(11,9,7,.78) 0%, rgba(11,9,7,.38) 30%, rgba(11,9,7,0) 60%),'
		+ 'radial-gradient(ellipse 60% 40% at 50% 26%, rgba(11,9,7,.32) 0%, rgba(11,9,7,0) 100%),'
		+ 'linear-gradient(180deg, rgba(11,9,7,.45) 0%, rgba(11,9,7,0) 18%)';

	CH.forEach(function (c) {
		var im = document.createElement('img'); im.src = c.img; im.alt = ''; fallback.appendChild(im);
		var box = document.createElement('div');
		if (c.fg) {
			var fg = document.createElement('img'); fg.src = c.fg; fg.alt = ''; box.appendChild(fg);
			var shade = document.createElement('i');
			shade.style.cssText = 'background:' + SHADE_CSS + ';-webkit-mask:url("' + c.fg + '") center/cover no-repeat;mask:url("' + c.fg + '") center/cover no-repeat;';
			box.appendChild(shade);
		}
		fgFallback.appendChild(box);
	});

	/* ---------- State ---------- */
	var active = 0;          // chapter on screen
	var from = 0, to = 0;    // the wipe goes from → to
	var p = 0;               // wipe progress 0..1
	var dir = 1;             // 1 = next (rises from below), -1 = previous (falls from above)
	var wipeAnim = null, busy = false;

	/* ---------- Text (Motion) ---------- */
	function parts(i) {
		return {
			letters: back.children[i].querySelectorAll('.anu-ch'),
			others: front.children[i].querySelectorAll('.anu-sc-meta, .anu-sc-deva, .anu-sc-more'),
			slides: [back.children[i], front.children[i]],
		};
	}
	function textOut(i, d) {
		var t = parts(i);
		t.slides.forEach(function (s) { s.classList.remove('is-active'); s.classList.add('is-leaving'); });
		if (reduced) { t.slides.forEach(function (s) { s.classList.remove('is-leaving'); }); return; }
		animate(t.letters, { y: ['0%', d > 0 ? '-55%' : '55%'], opacity: [1, 0], filter: ['blur(0px)', 'blur(10px)'] },
			{ duration: 0.5, ease: EASE_IN, delay: stagger(0.022, { from: d > 0 ? 'first' : 'last' }) });
		animate(t.others, { opacity: [1, 0], y: [0, d > 0 ? -16 : 16] }, { duration: 0.35, ease: EASE_IN })
			.then(function () { t.slides.forEach(function (s) { if (!s.classList.contains('is-active')) s.classList.remove('is-leaving'); }); });
	}
	function textIn(i, d, delay) {
		var t = parts(i);
		t.slides.forEach(function (s) { s.classList.remove('is-leaving'); s.classList.add('is-active'); });
		if (reduced) {
			animate(t.letters, { y: '0%', opacity: 1, filter: 'blur(0px)', rotateX: 0 }, { duration: 0 });
			animate(t.others, { opacity: 1, y: 0 }, { duration: 0 });
			return;
		}
		animate(t.letters,
			{ y: [d > 0 ? '95%' : '-95%', '0%'], rotateX: [d > 0 ? -80 : 80, 0], opacity: [0, 1], filter: ['blur(14px)', 'blur(0px)'] },
			{ duration: 1.1, ease: EASE_OUT, delay: stagger(0.045, { startDelay: delay }) });
		animate(t.others, { opacity: [0, 1], y: [d > 0 ? 22 : -22, 0] },
			{ duration: 0.8, ease: EASE_OUT, delay: stagger(0.09, { startDelay: delay + 0.35 }) });
	}
	// Everything but the first chapter starts hidden
	for (var k = 0; k < N; k++) {
		var t0 = parts(k);
		animate(t0.letters, { opacity: 0 }, { duration: 0 });
		animate(t0.others, { opacity: 0 }, { duration: 0 });
	}

	function markActive(i) {
		for (var k = 0; k < N; k++) dots[k].classList.toggle('is-on', k === i);
		counter.innerHTML = '<b>' + pad(i + 1) + '</b> / ' + pad(N);
		credit.textContent = CH[i].credit ? data.photo + ': ' + CH[i].credit : '';
		for (var j = 0; j < N; j++) {
			fallback.children[j].classList.toggle('is-on', j === i);
			fgFallback.children[j].classList.toggle('is-on', j === i && depthOn[j]);
		}
	}

	/* ---------- Moving between chapters ---------- */
	function finish() {
		active = to; from = to; p = 0; busy = false; wipeAnim = null;
	}
	function commit(i, d, startP) {
		// Text swap + finish the wipe from startP to 1
		textOut(active, d);
		textIn(i, d, 0.28);
		markActive(i);
		busy = true;
		var remaining = 1 - startP;
		wipeAnim = animate(startP, 1, {
			duration: reduced ? 0 : 1.5 * Math.max(0.35, remaining),
			ease: startP > 0 ? EASE_OUT : EASE_IN_OUT,
			onUpdate: function (v) { p = v; },
			onComplete: finish,
		});
	}
	function goTo(i) {
		i = clamp(i, 0, N - 1);
		if (busy || dragging || i === active) return;
		from = active; to = i; dir = i > active ? 1 : -1; p = 0;
		commit(i, dir, 0);
	}
	Array.prototype.forEach.call(dots, function (b, i) { b.addEventListener('click', function () { goTo(i); }); });

	/* Drag: scrub the wipe, then snap forward or back */
	var dragging = false, startY = 0;
	stage.addEventListener('pointerdown', function (e) {
		if (e.button > 0 || busy) return;
		dragging = true; startY = e.clientY;
		stage.classList.add('is-dragging');
	});
	window.addEventListener('pointermove', function (e) {
		if (!dragging) return;
		var dy = startY - e.clientY; // up = next
		var d = dy >= 0 ? 1 : -1, cand = active + d;
		if (cand < 0 || cand >= N) { p = 0; to = from = active; return; }
		from = active; to = cand; dir = d;
		p = clamp(Math.abs(dy) / (window.innerHeight * 0.6), 0, 1) * 0.85;
	});
	function release() {
		if (!dragging) return;
		dragging = false; stage.classList.remove('is-dragging');
		if (to !== active && p > 0.2) {
			commit(to, dir, p);
		} else if (p > 0) {
			busy = true;
			wipeAnim = animate(p, 0, { duration: 0.6, ease: EASE_OUT, onUpdate: function (v) { p = v; }, onComplete: function () { to = from = active; p = 0; busy = false; } });
		}
	}
	window.addEventListener('pointerup', release);
	window.addEventListener('pointercancel', release);

	var wheelAcc = 0, wheelLock = 0;
	window.addEventListener('wheel', function (e) {
		e.preventDefault();
		var now = performance.now();
		if (busy || now < wheelLock) return;
		wheelAcc += e.deltaY;
		if (Math.abs(wheelAcc) > 40) { goTo(active + Math.sign(wheelAcc)); wheelAcc = 0; wheelLock = now + 400; }
	}, { passive: false });
	window.addEventListener('keydown', function (e) {
		if (e.target && /input|textarea|select/i.test(e.target.tagName)) return;
		if (['ArrowDown', 'PageDown', ' '].indexOf(e.key) > -1) { e.preventDefault(); goTo(active + 1); }
		if (['ArrowUp', 'PageUp'].indexOf(e.key) > -1) { e.preventDefault(); goTo(active - 1); }
	});

	/* ---------- Depth on/off per chapter (like iOS: off when the subject hides too much of the title) ---------- */
	var MAX_COVER = 0.42;
	var depthOn = CH.map(function () { return false; });
	var fgImages = [];
	var probe = document.createElement('canvas');
	var probeCtx = probe.getContext('2d', { willReadFrequently: true });
	function measureDepth() {
		var W = window.innerWidth, H = window.innerHeight, S = 0.25;
		probe.width = Math.ceil(W * S); probe.height = Math.ceil(H * S);
		CH.forEach(function (c, i) {
			var im = fgImages[i];
			if (!im) { depthOn[i] = false; return; }
			var r = back.children[i].querySelector('.anu-sc-title').getBoundingClientRect(); // the h2 box itself is never transformed
			var s = Math.max(W / im.naturalWidth, H / im.naturalHeight), w = im.naturalWidth * s, h = im.naturalHeight * s;
			probeCtx.clearRect(0, 0, probe.width, probe.height);
			probeCtx.drawImage(im, (W - w) / 2 * S, (H - h) / 2 * S, w * S, h * S);
			var x0 = clamp(Math.floor(r.left * S), 0, probe.width - 1), y0 = clamp(Math.floor(r.top * S), 0, probe.height - 1);
			var rw = Math.max(1, Math.min(probe.width - x0, Math.ceil(r.width * S))), rh = Math.max(1, Math.min(probe.height - y0, Math.ceil(r.height * S)));
			var px = probeCtx.getImageData(x0, y0, rw, rh).data, covered = 0;
			for (var q = 3; q < px.length; q += 4) if (px[q] > 128) covered++;
			var share = covered / (rw * rh);
			depthOn[i] = share > 0.003 && share < MAX_COVER;
			back.children[i].dataset.depth = depthOn[i] ? Math.round(share * 100) + '%' : 'off';
		});
		markActive(busy ? to : active);
	}

	/* ---------- WebGL ---------- */
	var VS = 'attribute vec2 p; varying vec2 vUv; void main(){ vUv = p*0.5+0.5; gl_Position = vec4(p,0.,1.); }';
	var COMMON = [
		'precision highp float;',
		'varying vec2 vUv;',
		'uniform sampler2D t0, t1;',
		'uniform vec2 s0, s1, res;',
		'uniform float prog, dir, time, on0, on1;',
		'vec2 cover(vec2 uv, vec2 img){ float ra = res.x/res.y, ri = img.x/img.y; vec2 sc = ra > ri ? vec2(1., ri/ra) : vec2(ra/ri, 1.); return (uv-.5)*sc + .5; }',
		'float ease(float t){ return t < .5 ? 4.*t*t*t : 1. - pow(-2.*t + 2., 3.)/2.; }',
		// Column wipe: 6 slices, each starting a little after the previous one
		'void wipe(out float m, out float cp, out float edge){',
		'  float COLS = 6., STEP = .11;',
		'  float col = floor(vUv.x*COLS); float k = dir > 0. ? col : COLS-1.-col;',
		'  cp = ease(clamp(prog*(1. + (COLS-1.)*STEP) - k*STEP, 0., 1.));',
		'  edge = dir > 0. ? cp*1.004 : 1. - cp*1.004;',
		'  float aa = 1.5/res.y;',
		'  m = dir > 0. ? 1. - smoothstep(edge-aa, edge+aa, vUv.y) : smoothstep(edge-aa, edge+aa, vUv.y);',
		'}',
		'void coords(float cp, out vec2 ua, out vec2 ub){',
		'  vec2 base = (vUv-.5)*(1. - .03*sin(time*.12)) + .5;', // slow breathing zoom
		'  ua = (base-.5)*(1. - cp*.07) + .5;',                   // old photo eases back
		'  ub = base + vec2(0., dir*(1.-cp)*.2);',                // new photo rises (or falls) into place
		'}',
		'vec3 grade(vec3 col){ col = mix(col, col*vec3(1.04,.99,.94), .4); float v = smoothstep(1.3, .4, length(vUv-.5)*1.4); return col*mix(.75, 1., v); }'
	].join('\n');
	var FS_PHOTO = COMMON + [
		'void main(){ float m, cp, edge; wipe(m, cp, edge); vec2 ua, ub; coords(cp, ua, ub);',
		'  vec3 a = texture2D(t0, cover(ua, s0)).rgb * (1. - cp*.5);',
		'  vec3 b = texture2D(t1, cover(ub, s1)).rgb;',
		'  vec3 col = grade(mix(a, b, m));',
		'  float live = step(.001, cp) * step(cp, .999);',          // turmeric line on the moving edge
		'  float line = live * (1. - smoothstep(0., 2.2/res.y, abs(vUv.y - edge)));',
		'  col = mix(col, vec3(.96,.72,0.), line*.95);',
		'  gl_FragColor = vec4(col, 1.);',
		'}'
	].join('\n');
	var FS_SUBJECT = COMMON + [
		'vec3 shade(vec3 col){ float yt = 1.-vUv.y; vec3 ink = vec3(11.,9.,7.)/255.;',
		'  float b = yt > .4 ? (yt > .7 ? mix(.38,.78,(yt-.7)/.3) : mix(0.,.38,(yt-.4)/.3)) : 0.;',
		'  float r = .32*(1.-clamp(length(vec2((vUv.x-.5)/.6,(yt-.26)/.4)),0.,1.));',
		'  float t = yt < .18 ? mix(.45,0.,yt/.18) : 0.;',
		'  col = mix(col, ink, b); col = mix(col, ink, r); col = mix(col, ink, t); return col; }',
		'void main(){ float m, cp, edge; wipe(m, cp, edge); vec2 ua, ub; coords(cp, ua, ub);',
		'  vec4 a = texture2D(t0, cover(ua, s0)); a.rgb *= 1. - cp*.5; a.a *= on0;',
		'  vec4 b = texture2D(t1, cover(ub, s1)); b.a *= on1;',
		'  vec4 c = vec4(a.rgb*a.a, a.a)*(1.-m) + vec4(b.rgb*b.a, b.a)*m;',
		'  vec3 rgb = c.a > .001 ? c.rgb / c.a : vec3(0.);',
		'  gl_FragColor = vec4(shade(grade(rgb))*c.a, c.a);',
		'}'
	].join('\n');

	function Renderer(canvas, fs, alpha) {
		var gl = canvas.getContext('webgl', { antialias: false, premultipliedAlpha: true, alpha: alpha });
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
		['t0', 't1', 's0', 's1', 'res', 'prog', 'dir', 'time', 'on0', 'on1'].forEach(function (k) { uni[k] = gl.getUniformLocation(prog, k); });
		gl.uniform1i(uni.t0, 0); gl.uniform1i(uni.t1, 1);
		gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
		var textures = [];
		this.sizes = [];
		var sizes = this.sizes;
		this.setImages = function (list, fmt) {
			list.forEach(function (im, i) {
				var t = gl.createTexture(); gl.bindTexture(gl.TEXTURE_2D, t);
				if (im) gl.texImage2D(gl.TEXTURE_2D, 0, fmt, fmt, gl.UNSIGNED_BYTE, im);
				else gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1, 1, 0, gl.RGBA, gl.UNSIGNED_BYTE, new Uint8Array(4));
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
		this.draw = function (i0, i1, pr, d, time, on0, on1, sizeOf) {
			gl.clearColor(0, 0, 0, 0); gl.clear(gl.COLOR_BUFFER_BIT);
			gl.activeTexture(gl.TEXTURE0); gl.bindTexture(gl.TEXTURE_2D, textures[i0]);
			gl.activeTexture(gl.TEXTURE1); gl.bindTexture(gl.TEXTURE_2D, textures[i1]);
			var a = sizeOf(i0), b = sizeOf(i1);
			gl.uniform2f(uni.s0, a[0], a[1]); gl.uniform2f(uni.s1, b[0], b[1]);
			gl.uniform1f(uni.prog, pr); gl.uniform1f(uni.dir, d); gl.uniform1f(uni.time, time);
			gl.uniform1f(uni.on0, on0); gl.uniform1f(uni.on1, on1);
			gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
		};
		this.resize();
	}

	/* ---------- Load & run ---------- */
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

	Promise.all([
		Promise.all(CH.map(function (c) { return load(c.img); })),
		Promise.all(CH.map(function (c) { return load(c.fg); })),
		document.fonts ? document.fonts.ready : null,
	]).then(function (res) {
		var photos = res[0]; fgImages = res[1];
		var photoGL = null, subjectGL = null;
		if (photos.every(Boolean)) {
			try {
				photoGL = new Renderer(root.querySelector('.anu-sc-gl'), FS_PHOTO, false);
				photoGL.setImages(photos, WebGLRenderingContext.RGB);
				if (fgImages.some(Boolean)) {
					subjectGL = new Renderer(root.querySelector('.anu-sc-fg-gl'), FS_SUBJECT, true);
					subjectGL.setImages(fgImages, WebGLRenderingContext.RGBA);
				}
			} catch (err) { photoGL = null; subjectGL = null; }
		}
		if (!photoGL) root.classList.add('no-webgl');

		measureDepth();
		var rt;
		window.addEventListener('resize', function () {
			if (photoGL) photoGL.resize();
			if (subjectGL) subjectGL.resize();
			clearTimeout(rt); rt = setTimeout(measureDepth, 150);
		});

		markActive(0);
		setTimeout(function () {
			loader.classList.add('is-done');
			textIn(0, 1, 0.25); // first title arrives as the loader fades
		}, 350);

		var photoSize = function (i) { return photoGL.sizes[i]; };
		function loop(now) {
			if (photoGL) {
				var time = reduced ? 0 : now / 1000;
				photoGL.draw(from, to, p, dir, time, 1, 1, photoSize);
				if (subjectGL) subjectGL.draw(from, to, p, dir, time, depthOn[from] ? 1 : 0, depthOn[to] ? 1 : 0, photoSize);
			}
			requestAnimationFrame(loop);
		}
		requestAnimationFrame(loop);
	});
})();
