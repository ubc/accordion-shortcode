<?php
/*
Plugin Name: Accordion Shortcode
Plugin URI: http://wordpress.org/extend/plugins/accordion-shortcode/
Description: Adds shortcode that enables you to create accordions
Author: CTLT
Version: 2.1.2
Author URI: http://ctlt.ubc.ca
*/

/**
 * OLT_Accordion_Shortcode class.
 */
class OLT_Accordion_Shortcode {

	static $add_script;
	static $shortcode_count;
	static $shortcode_js_data;
	static $support;
	static $current_accordion_id;
	static $current_active_content;

	/**
	 * init function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function init() {

		add_shortcode( 'accordion', array( __CLASS__, 'accordion_shortcode' ) );
		add_shortcode( 'accordions', array( __CLASS__, 'accordions_shortcode' ) );

		add_action( 'init', array( __CLASS__, 'register_script' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_style' ) );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) );

		/* Apply filters to the tabs content. */
		add_filter( 'accordion_content', 'wpautop' );
		add_filter( 'accordion_content', 'shortcode_unautop' );
		add_filter( 'accordion_content', 'do_shortcode' );

		self::$shortcode_count = 0;
		add_filter( 'no_texturize_shortcodes', array( __CLASS__, 'no_texturize_shortcodes_remove_accordions_from_texturize' ) );

	}

	/**
	 * accordion_shortcode function.
	 *
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static  function accordion_shortcode( $atts, $content ) {
		global $post;

		$selected = ( self::$current_active_content == self::$shortcode_count ? true : false );
		extract(shortcode_atts(array(
					'title' => null,
					'class' => null,
					'before_shell' => '',
					'after_shell'  => '',
					'before' => '',
					'after'  => '',
					'heading_tag' => 'h3',
					'heading_link_attr' => '',
					'heading_attr' => '',
				), apply_filters( 'accordion-shortcode-atts', $atts, $selected ) ) );

		ob_start();

		$title 		= ( empty( $title ) ? $post->post_title : $title );
		$id 		= substr( trim( sanitize_title_with_dashes( $title ) ), 0, 30 ).'-'.self::$shortcode_count;

		if ( empty( $title ) ) :

			return '<span style="color:red">Please enter a title attribute like [accordion title="title name"] accordion content [accordion]</span>';

		endif;

		self::$shortcode_count++;

		$str  = $before_shell;
		$str .= '<'. $heading_tag .' '. $heading_attr .'><a href="#'. $id .'" '. $heading_link_attr .'>'. $title .'</a></'. $heading_tag .'>';
		$str .= '<div id="'. $id .'" class="accordian-shortcode-content '. $class. '" >'. $before. apply_filters( 'accordion_content', $content ) . $after. '</div>';
		$str .= $after_shell;

		return $str;
	}

	/**
	 * eval_bool function.
	 *
	 * @access public
	 * @static
	 * @param mixed $item
	 * @return bool
	 */
	static function eval_bool( $item ) {

		return ( 'false' == (string) $item || 'null' == (string) $item || '0' == (string) $item || empty( $item ) ? false : true );

	}

	/**
	 * accordions_shortcode function.
	 *
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static function accordions_shortcode( $atts, $content ) {
		global $wp_version;
		self::$add_script = true;
		if ( is_string( $atts ) ) :

			$atts = array();

		endif;

		$atts = apply_filters( 'accordion-shortcode-accordion-atts', $atts );

		if ( version_compare( $wp_version, '3.5', '>=' ) ) :
			// AFTER 3.5
			$defaults = array(
					'heightstyle' => 'content',
					'autoheight' => false,
					'disabled' => false,
					'active' => 0,
					'clearstyle'  => false,
					'collapsible' => false,
					'fillspace' => false,
					'before' => '',
					'after' => '',
					'class' => '',
					'style' => '',
				);
		else :
			// PRE 3.5

			$defaults = array(
					'autoheight' => false,
					'disabled' => false,
					'active' => 0,
					'clearstyle'  => false,
					'collapsible' => false,
					'fillspace' => false,
					'before' => '',
					'after' => '',
					'class' => '',
					'style' => '',
				);
		endif;

		$atts = shortcode_atts( $defaults , apply_filters( 'accordions-shortcode-atts', $atts ) );

		if ( version_compare( $wp_version, '3.5', '>=' ) ) :
			$attr['heightStyle'] = $atts['heightstyle'];
		endif;
		$attr['autoHeight'] = self::eval_bool( $atts['autoheight'] );
		$attr['disabled']  	= self::eval_bool( $atts['disabled'] );
		$attr['clearStyle'] = self::eval_bool( $atts['clearstyle'] );

		$attr['collapsible'] = self::eval_bool( $atts['collapsible'] );
		$attr['fillSpace'] 	= self::eval_bool( $atts['fillspace'] );

		if ( $attr['collapsible'] ) {
			if ( ! is_int( $atts['active'] ) ) {
				//is_bool($atts['active']) && !$atts['active'])
				if ( ! self::eval_bool( $atts['active'] ) ) {

					//confilct between none collab and other themes
					if ( ! array_key_exists( 0, self::$support ) || 'twitter-bootstrap' != self::$support[0] ) :

						$attr['active'] = false;

					else :

						$attr['active'] = -1;

					endif;

				} else {
					$attr['active'] = isset( $defaults['active'] ) && $defaults['active'] ? $defaults['active'] : 0;
				}
			} else {
				$attr['active'] = $atts['active'];
			}
		} else {
			$attr['active'] = (int) $atts['active'];
		}

		self::$current_active_content = $attr['active'];

		self::$current_accordion_id = 'random-accordion-id-'.rand( 0,1000 );

		$content = str_replace( ']<br />',']', ( substr( $content, 0 , 6 ) == '<br />' ? substr( $content, 6 ): $content ) );

		self::$shortcode_js_data[ self::$current_accordion_id ] = $attr;

		return str_replace( "\r\n", '', '<div id="'.self::$current_accordion_id.'" class="accordion-shortcode '.$atts['class'].' '. esc_attr( $atts['style'] ) .'">'.$atts['before'].do_shortcode( $content ).$atts['after'].'</div><!-- #'.self::$current_accordion_id.'end of accordion shortcode -->' );

	}

	/**
	 * register_script function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function register_script() {

		self::$support = get_theme_support( 'accordions' );

		if ( ! is_array( self::$support ) ) {
			self::$support = array();
		}

		if ( ! array_key_exists( 0, self::$support ) || 'twitter-bootstrap' !== self::$support[0] ) {
			wp_register_script( 'accordion-shortcode', plugins_url( 'accordion.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ), '1.0', true );
		}

		wp_register_style( 'accordion-shortcode',  plugins_url( 'accordion.css', __FILE__ ) );
		wp_register_style( 'accordion-shortcode-accessibility-fix',  plugins_url( 'fix-focus-accessibility.css', __FILE__ ) );

		wp_register_script( 'accordion-shortcode-accessibility-fix' , plugins_url( 'fix-focus-accessibility.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ), '1.0', true );

		if ( array_key_exists( 0, self::$support ) && 'twitter-bootstrap' === self::$support[0] ) {
			require_once 'support/twitter-bootstrap/action.php';
		}
	}

	/**
	 * enqueue_style function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function enqueue_style() {
		if ( empty( self::$support ) ) :

			wp_enqueue_style( 'accordion-shortcode' );

			wp_enqueue_style( 'accordion-shortcode-accessibility-fix' );

		endif;
	}

	/**
	 * print_script function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function print_script() {

		if ( ! self::$add_script ) :

			return;

		endif;

		if ( ! array_key_exists( 0, self::$support ) || 'twitter-bootstrap' !== self::$support[0] ) {
			wp_enqueue_script( 'accordion-shortcode' );
			wp_localize_script( 'accordion-shortcode', 'accordion_shortcode', self::$shortcode_js_data );
		}

		wp_enqueue_script( 'accordion-shortcode-accessibility-fix' );
		wp_localize_script( 'accordion-shortcode-accessibility-fix', 'accordion_shortcode_fix', self::$shortcode_js_data );
	}

		public static function no_texturize_shortcodes_remove_accordions_from_texturize( $shortcodes ) {

		$shortcodes[] = 'accordion';
		$shortcodes[] = 'accordions';

		return $shortcodes;

		}/* no_texturize_shortcodes_remove_accordions_from_texturize fixes filters not using proper shortcode API https://wordpress.org/support/topic/issues-with-wordpress-401-and-shortcodes and ticket https://core.trac.wordpress.org/ticket/29557 */
}
// lets play
OLT_Accordion_Shortcode::init();
