<?php



function accordion_shortcode_twitter_bootstrap_atts( $atts, $selected ) {

	$atts['heading_link_attr'] = 'class="accordion-toggle ' . esc_attr( $atts['style'] ) . '" data-toggle="collapse" data-parent="#'.OLT_Accordion_Shortcode::$current_accordion_id.'"';
	$atts['heading_tag']	= 'div';
	$atts['heading_attr'] 	= ' class="accordion-heading" ';

	$atts['class'] 			= ($selected ? 'accordion-body collapse in' : 'accordion-body collapse' );
	$atts['before_shell'] 	= '<div class="accordion-group">';
	$atts['after_shell'] 	= '</div>';
	$atts['before'] 		= '<div class="accordion-inner">';
	$atts['after'] 			= '</div>';

	return $atts;

}

add_action( 'accordion-shortcode-atts', 'accordion_shortcode_twitter_bootstrap_atts', 10 , 2 );


function accordions_shortcode_twitter_bootstrap_atts( $atts ) {

	$atts['class'] 	= 'accordion';

	return $atts;

}

add_action( 'accordions-shortcode-atts', 'accordions_shortcode_twitter_bootstrap_atts' );
