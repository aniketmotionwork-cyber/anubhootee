/* Editor previews for Anubhootee's server-rendered blocks (they render fully on the site). */
(function (wp) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var labels = {
		'anubhootee/showcase': 'Chapter showcase: full-screen chapters from Posts → Categories (shows on the live site)',
		'anubhootee/story-hero': 'Story hero: featured image, category, title and excerpt',
		'anubhootee/byline': 'Byline: author, date, share links',
		'anubhootee/next-story': 'Next story: large photo link',
		'anubhootee/logo': 'ANUBHOOTEE',
		'anubhootee/lang-switch': 'EN · मराठी',
		'anubhootee/copyright': '© Anubhootee',
		'anubhootee/policy-links': 'Contact · Shipping · Refunds · Privacy · Terms'
	};
	Object.keys(labels).forEach(function (name) {
		wp.blocks.registerBlockType(name, {
			edit: function () {
				var small = name === 'anubhootee/logo' || name === 'anubhootee/lang-switch' || name === 'anubhootee/copyright' || name === 'anubhootee/policy-links';
				return el('div', useBlockProps({
					style: small
						? { fontSize: '13px', letterSpacing: name === 'anubhootee/logo' ? '.3em' : '.1em', fontWeight: 600 }
						: { padding: '28px 20px', border: '1px dashed #8A8279', background: '#FAF6EF', color: '#4A443D', fontSize: '13px', textAlign: 'center' }
				}), labels[name]);
			},
			save: function () { return null; }
		});
	});
})(window.wp);
