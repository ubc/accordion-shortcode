jQuery(document).ready(function($) {
	$.each(accordion_shortcode, function(id, attr) {

		$("#" + id).accordion(attr);
	});
	if (location.hash) {
		$('a[href="'+location.hash+'"]').trigger('click');
	}
});
