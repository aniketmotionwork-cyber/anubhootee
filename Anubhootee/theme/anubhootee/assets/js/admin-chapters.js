/* Cover photo picker on Posts → Categories */
jQuery(function ($) {
	var frame;
	$(document).on('click', '.anu-cover-pick', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('td, .form-field');
		frame = wp.media({ title: 'Chapter cover photo', library: { type: 'image' }, multiple: false, button: { text: 'Use this photo' } });
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			var url = (a.sizes && (a.sizes.medium || a.sizes.full) || a).url;
			wrap.find('.anu-cover-id').val(a.id);
			wrap.find('.anu-cover-preview').html('<img src="' + url + '" style="max-width:260px;height:auto;display:block">');
		});
		frame.open();
	});
	$(document).on('click', '.anu-cover-clear', function (e) {
		e.preventDefault();
		var wrap = $(this).closest('td, .form-field');
		wrap.find('.anu-cover-id').val('');
		wrap.find('.anu-cover-preview').empty();
	});
	/* Category "Add" form is submitted with AJAX: clear the picker afterwards. */
	$(document).ajaxSuccess(function (e, xhr, settings) {
		if (settings.data && settings.data.indexOf('action=add-tag') !== -1) {
			$('#addtag .anu-cover-id').val('');
			$('#addtag .anu-cover-preview').empty();
		}
	});
});
