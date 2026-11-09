/* Anubhootee: header state, reading progress, reveal, copy link */
(function () {
	document.documentElement.classList.add('anu-js');

	var header = document.querySelector('.anu-header.is-overlay');
	var article = document.querySelector('.anu-article');
	var bar = null;

	if (article) {
		bar = document.createElement('div');
		bar.className = 'anu-progress';
		document.body.appendChild(bar);
	}

	function onScroll() {
		var h = document.documentElement;
		if (header) header.classList.toggle('is-solid', window.scrollY > window.innerHeight * 0.75);
		if (bar) {
			var max = h.scrollHeight - window.innerHeight;
			bar.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
		}
	}
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	if (article && 'IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		Array.prototype.forEach.call(article.children, function (el) { io.observe(el); });
	} else if (article) {
		Array.prototype.forEach.call(article.children, function (el) { el.classList.add('is-in'); });
	}

	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-anu-copy]');
		if (!btn) return;
		e.preventDefault();
		var done = function () {
			var old = btn.textContent;
			btn.textContent = btn.getAttribute('data-copied') || 'Copied';
			setTimeout(function () { btn.textContent = old; }, 1600);
		};
		if (navigator.clipboard) navigator.clipboard.writeText(btn.getAttribute('data-anu-copy')).then(done, done);
		else done();
	});
})();
