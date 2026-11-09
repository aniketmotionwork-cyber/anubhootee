/* Anubhootee: chapter showcase (drag / wheel / keys, WebGL liquid transition) */
(function () {
	var root = document.querySelector('.anu-showcase');
	if (!root) return;

	var data = JSON.parse(root.querySelector('.anu-sc-data').textContent);
	var CH = data.chapters, N = CH.length;
	var stage = root.querySelector('.anu-sc-stage');
	var slidesEl = root.querySelector('.anu-sc-slides');
	var slides = slidesEl.children;
	var dots = root.querySelectorAll('.anu-sc-progress button');
	var counter = root.querySelector('.anu-sc-counter');
	var credit = root.querySelector('.anu-sc-credit');
	var fallback = root.querySelector('.anu-sc-fallback');
	var loader = root.querySelector('.anu-sc-loader');
	var loadbar = root.querySelector('.anu-sc-loader__road');
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var pad = function (n) { return String(n).padStart(2, '0'); };
	var clamp = function (v, a, b) { return Math.max(a, Math.min(b, v)); };

	CH.forEach(function (c) { var im = document.createElement('img'); im.src = c.img; im.alt = ''; fallback.appendChild(im); });

	/* ---------- Physics ---------- */
	var target = 0, current = 0, active = -1, velocity = 0;
	var gap = function () { return Math.min(window.innerHeight * 0.62, 560); };
	function setGap() { root.style.setProperty('--sc-gap', gap() + 'px'); }
	setGap();
	window.addEventListener('resize', function () { setGap(); resizeGL(); });
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
		for (var k = 0; k < N; k++) {
			slides[k].classList.toggle('is-active', k === i);
			dots[k].classList.toggle('is-on', k === i);
			fallback.children[k].classList.toggle('is-on', k === i);
		}
		counter.innerHTML = '<b>' + pad(i + 1) + '</b> / ' + pad(N);
		credit.textContent = CH[i].credit ? data.photo + ': ' + CH[i].credit : '';
	}

	/* ---------- WebGL ---------- */
	var canvas = root.querySelector('.anu-sc-gl');
	var gl = null;
	try { gl = canvas.getContext('webgl', { antialias: false, premultipliedAlpha: false }); } catch (err) { gl = null; }
	var prog, uni = {}, textures = [], sizes = [];
	var VS = 'attribute vec2 p; varying vec2 vUv; void main(){ vUv = p*0.5+0.5; gl_Position = vec4(p,0.,1.); }';
	var FS = [
		'precision highp float;',
		'varying vec2 vUv;',
		'uniform sampler2D t0, t1;',
		'uniform vec2 s0, s1, res;',
		'uniform float prog, vel, time;',
		'float h(vec2 p){ return fract(sin(dot(p, vec2(127.1,311.7)))*43758.5453); }',
		'float n(vec2 p){ vec2 i=floor(p), f=fract(p); f=f*f*(3.-2.*f); return mix(mix(h(i),h(i+vec2(1,0)),f.x), mix(h(i+vec2(0,1)),h(i+vec2(1,1)),f.x), f.y); }',
		'float fbm(vec2 p){ float v=0., a=.5; for(int i=0;i<4;i++){ v+=a*n(p); p*=2.03; a*=.5; } return v; }',
		'vec2 cover(vec2 uv, vec2 img){ float ra = res.x/res.y, ri = img.x/img.y; vec2 sc = ra > ri ? vec2(1., ri/ra) : vec2(ra/ri, 1.); return (uv-.5)*sc + .5; }',
		'vec3 samp(sampler2D t, vec2 uv, vec2 sz, float split){ vec2 c = cover(uv, sz); return vec3(texture2D(t, c + vec2(split,0.)).r, texture2D(t, c).g, texture2D(t, c - vec2(split,0.)).b); }',
		'void main(){',
		'  vec2 uv = vUv;',
		'  float nn = fbm(uv*3.2 + vec2(0., time*.05));',
		'  float w = .18;',
		'  float edge = (1.-uv.y)*.55 + nn*.45;',
		'  float t = prog*(1.+2.*w) - w;',
		'  float m = smoothstep(edge-w, edge+w, t);',
		'  float wave = sin(uv.y*9. + time*1.6)*.008*abs(vel);',
		'  float zoom = 1. - .04*sin(time*.12);',
		'  vec2 base = (uv-.5)*zoom + .5 + vec2(wave, 0.);',
		'  vec3 a = samp(t0, base + vec2(0., -m*.06*nn), s0, .0015*abs(vel));',
		'  vec3 b = samp(t1, base + vec2(0., (1.-m)*.06*nn), s1, .0015*abs(vel));',
		'  vec3 col = mix(a, b, m);',
		'  col = mix(col, col*vec3(1.04,.99,.94), .4);',
		'  float v = smoothstep(1.3, .4, length(uv-.5)*1.4);',
		'  col *= mix(.75, 1., v);',
		'  gl_FragColor = vec4(col, 1.);',
		'}'
	].join('\n');

	function sh(type, src) {
		var s = gl.createShader(type); gl.shaderSource(s, src); gl.compileShader(s);
		if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) throw new Error(gl.getShaderInfoLog(s));
		return s;
	}
	function initGL() {
		prog = gl.createProgram();
		gl.attachShader(prog, sh(gl.VERTEX_SHADER, VS)); gl.attachShader(prog, sh(gl.FRAGMENT_SHADER, FS));
		gl.linkProgram(prog); gl.useProgram(prog);
		var buf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buf);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW);
		var loc = gl.getAttribLocation(prog, 'p'); gl.enableVertexAttribArray(loc); gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
		['t0', 't1', 's0', 's1', 'res', 'prog', 'vel', 'time'].forEach(function (k) { uni[k] = gl.getUniformLocation(prog, k); });
		gl.uniform1i(uni.t0, 0); gl.uniform1i(uni.t1, 1);
		gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
		resizeGL();
	}
	function resizeGL() {
		if (!gl || !prog) return;
		var dpr = Math.min(window.devicePixelRatio || 1, 2);
		canvas.width = window.innerWidth * dpr; canvas.height = window.innerHeight * dpr;
		gl.viewport(0, 0, canvas.width, canvas.height);
		gl.uniform2f(uni.res, canvas.width, canvas.height);
	}
	function makeTex(img) {
		var t = gl.createTexture(); gl.bindTexture(gl.TEXTURE_2D, t);
		gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, img);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
		return t;
	}

	/* ---------- Load & loop ---------- */
	var loaded = 0;
	var loads = CH.map(function (c) {
		return new Promise(function (res) {
			var im = new Image();
			im.crossOrigin = 'anonymous';
			im.onload = function () { loaded++; loadbar.style.width = (loaded / N * 100) + '%'; res(im); };
			im.onerror = function () { loaded++; res(null); };
			im.src = c.img;
		});
	});
	Promise.all(loads).then(function (list) {
		var useGL = !!gl && list.every(Boolean);
		if (useGL) {
			try {
				initGL();
				list.forEach(function (im, i) { textures[i] = makeTex(im); sizes[i] = [im.naturalWidth, im.naturalHeight]; });
			} catch (err) { useGL = false; }
		}
		if (!useGL) root.classList.add('no-webgl');
		setActive(0);
		setTimeout(function () { loader.classList.add('is-done'); }, 350);
		var last = 0;
		function loop(now) {
			var dt = Math.min(64, now - last || 16); last = now;
			var prev = current, k = dragging ? 0.35 : 0.085;
			current += (target - current) * (1 - Math.pow(1 - k, dt / 16.67));
			velocity += ((current - prev) * 60 - velocity) * 0.2;
			slidesEl.style.transform = 'translateY(' + (-current * gap() - gap() / 2) + 'px)';
			setActive(clamp(Math.round(current), 0, N - 1));
			if (useGL) {
				var c = clamp(current, 0, N - 1), i0 = Math.floor(c), p = c - i0, i1 = Math.min(i0 + 1, N - 1);
				if (reduced) p = p > 0.5 ? 1 : 0;
				gl.activeTexture(gl.TEXTURE0); gl.bindTexture(gl.TEXTURE_2D, textures[i0]);
				gl.activeTexture(gl.TEXTURE1); gl.bindTexture(gl.TEXTURE_2D, textures[i1]);
				gl.uniform2f(uni.s0, sizes[i0][0], sizes[i0][1]); gl.uniform2f(uni.s1, sizes[i1][0], sizes[i1][1]);
				gl.uniform1f(uni.prog, p);
				gl.uniform1f(uni.vel, reduced ? 0 : clamp(velocity, -3, 3));
				gl.uniform1f(uni.time, reduced ? 0 : now / 1000);
				gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
			}
			requestAnimationFrame(loop);
		}
		requestAnimationFrame(loop);
	});
})();
