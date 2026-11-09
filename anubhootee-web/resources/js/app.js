import Alpine from 'alpinejs';
import './showcase.js';

window.Alpine = Alpine;
Alpine.start();

/* Header state, reading progress, reveal on scroll, copy link */
(function () {
	document.documentElement.classList.add('anu-js');

	const header = document.querySelector('.anu-header.is-overlay');
	const article = document.querySelector('.anu-story .anu-article');
	let bar = null;

	if (article) {
		bar = document.createElement('div');
		bar.className = 'anu-progress';
		document.body.appendChild(bar);
	}

	function onScroll() {
		const h = document.documentElement;
		if (header && !document.querySelector('.anu-showcase')) {
			header.classList.toggle('is-solid', window.scrollY > window.innerHeight * 0.75);
		}
		if (bar) {
			const max = h.scrollHeight - window.innerHeight;
			bar.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
		}
	}
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	if (article && 'IntersectionObserver' in window) {
		const io = new IntersectionObserver((entries) => {
			entries.forEach((e) => {
				if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		Array.from(article.children).forEach((el) => io.observe(el));
	} else if (article) {
		Array.from(article.children).forEach((el) => el.classList.add('is-in'));
	}

	/* Story hero depth effect: like iOS, turn it off when the subject would hide too much of the title. */
	const depthHero = document.querySelector('.anu-hero.has-depth');
	if (depthHero) {
		const fg = depthHero.querySelector('.anu-hero__fg img');
		const title = depthHero.querySelector('.anu-hero__inner--back h1');
		const check = () => {
			if (!fg.naturalWidth) return;
			const box = depthHero.getBoundingClientRect(), t = title.getBoundingClientRect();
			const S = 0.25, W = Math.ceil(box.width * S), H = Math.ceil(box.height * S);
			const c = document.createElement('canvas'); c.width = W; c.height = H;
			const ctx = c.getContext('2d', { willReadFrequently: true });
			const s = Math.max(box.width / fg.naturalWidth, box.height / fg.naturalHeight);
			const w = fg.naturalWidth * s, h = fg.naturalHeight * s;
			ctx.drawImage(fg, (box.width - w) / 2 * S, (box.height - h) / 2 * S, w * S, h * S);
			const x0 = Math.max(0, Math.floor((t.left - box.left) * S)), y0 = Math.max(0, Math.floor((t.top - box.top) * S));
			const rw = Math.max(1, Math.min(W - x0, Math.ceil(t.width * S))), rh = Math.max(1, Math.min(H - y0, Math.ceil(t.height * S)));
			const px = ctx.getImageData(x0, y0, rw, rh).data;
			let covered = 0;
			for (let p = 3; p < px.length; p += 4) if (px[p] > 128) covered++;
			const share = covered / (rw * rh);
			depthHero.classList.toggle('no-depth', share < 0.003 || share > 0.42);
			depthHero.dataset.depth = Math.round(share * 100) + '%';
		};
		const run = () => (document.fonts ? document.fonts.ready : Promise.resolve()).then(check);
		if (fg.complete) run(); else fg.addEventListener('load', run);
		let t;
		window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(check, 150); });
	}

	/* Shared-layout transitions: name the clicked element so the next page can morph it into place */
	document.addEventListener('click', (e) => {
		if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
		const card = e.target.closest('.anu-card, .anu-next');
		if (card) {
			const img = card.querySelector('img');
			const title = card.querySelector('h3, .anu-next__title');
			if (img) img.style.viewTransitionName = 'anu-photo';
			if (title) title.style.viewTransitionName = 'anu-heading';
			return;
		}
		const more = e.target.closest('.anu-sc-slides--front .anu-sc-more');
		if (more) {
			const i = Array.prototype.indexOf.call(more.closest('.anu-sc-slides').children, more.closest('.anu-sc-slide'));
			const title = document.querySelector('.anu-sc-slides--back').children[i]?.querySelector('.anu-sc-title');
			if (title) title.style.viewTransitionName = 'anu-title';
		}
	}, true);
	// Coming back with the browser's Back button: clear the names so they don't clash
	window.addEventListener('pageshow', () => {
		document.querySelectorAll('[style*="view-transition-name"]').forEach((el) => { el.style.viewTransitionName = ''; });
	});

	document.addEventListener('click', (e) => {
		const btn = e.target.closest('[data-anu-copy]');
		if (!btn) return;
		e.preventDefault();
		const done = () => {
			const old = btn.textContent;
			btn.textContent = btn.getAttribute('data-copied') || 'Copied';
			setTimeout(() => { btn.textContent = old; }, 1600);
		};
		if (navigator.clipboard) navigator.clipboard.writeText(btn.getAttribute('data-anu-copy')).then(done, done);
		else done();
	});
})();
