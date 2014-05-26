jQuery(document).ready(function($) {

	$( '.accordion-heading .accordion-toggle' ).on( 'click', function(){

		// When someone clicks on a toggle, we remove all of these items with a class of focus
		$( '.accordion-heading .accordion-toggle.focus' ).removeClass( 'focus' );

		$( this ).addClass( 'focus' );

	} );

});