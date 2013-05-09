<?php
/*
Plugin Name: YouTube Control Shortcode
Plugin URI: http://wordpress.org/extend/plugins/youtube-shortcode/
Description: Adds shortcode that enables you to create accordions
Author: Devindra Payment, CTLT
Version: 1.0
Author URI: http://ctlt.ubc.ca
*/

/**
 * YouTube_Control_Shortcode class.
 */
class YouTube_Control_Shortcode {
	static $counter = 0;
	static $title_counter;
	static $player_id = null;
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function init() {
		add_shortcode( 'yc_video',   array( __CLASS__, 'youtube_shortcode' ) );
		add_shortcode( 'yc_control', array( __CLASS__, 'control_shortcode' ) );
		add_shortcode( 'yc_title',   array( __CLASS__, 'title_shortcode' ) );
		
		add_action( 'init',               array( __CLASS__, 'register_script' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_style' ) );
		add_action( 'wp_footer',          array( __CLASS__, 'print_script' ) );
	}
	
	/**
	 * register_script function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function register_script() {
		wp_register_style( 'ytc-shortcode',  plugins_url( 'ytc-shortcode.css', __FILE__ ) );
		wp_register_script( 'ytc-shortcode' , plugins_url( 'ytc-shortcode.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		//wp_register_script( 'youtube-iframe_api', 'https://www.youtube.com/iframe_api' );
	}
	
	/**
	 * enqueue_style function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function enqueue_style() {
		wp_enqueue_style( 'ytc-shortcode' );
	}
	
	/**
	 * print_script function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	static function print_script() {
		//wp_enqueue_script( 'youtube-iframe_api' );
		wp_enqueue_script( 'ytc-shortcode' );
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
	public static function youtube_shortcode( $atts, $content ) {
		if ( ! isset( $atts['id'] ) ):
			return;
		endif;
		
		$defaults = array(
			'id'       => '',
			'title'    => "Browse Video Segments",
			'autoplay' => '0',
			'autohide' => '2',
			'theme'    => "dark",
			'ratio'    => "720:440", //Standard youtube ratio for 720p
		);
		
		if ( in_array( 'autoplay', $atts ) ):
			$atts['autoplay'] = 'true';
		endif;
		
		if ( in_array( 'autohide', $atts ) ):
			$atts['autohide'] = 'true';
		endif;
		
		$atts = shortcode_atts( $defaults, $atts );
		
		if ( $atts['autoplay'] == '1' || $atts['autoplay'] == 'true' ):
			$atts['autoplay'] = '1';
		else:
			$atts['autoplay'] = $defaults['autoplay'];
		endif;
		
		if ( $atts['autohide'] == '1' || $atts['autohide'] == 'true' ):
			$atts['autohide'] = '1';
		else:
			$atts['autohide'] = $defaults['autohide'];
		endif;
		
		if ( $atts['theme'] != "light" ):
			$atts['theme'] = $defaults['theme'];
		endif;
		
		self::$counter++;
		self::$title_counter = 0;
		self::$player_id = 'ytplayer-'.self::$counter;
		
		$ratio = split( ':', $atts['ratio'] );
		$percentage = $ratio[1] / $ratio[0] * 100;
		
		$content = do_shortcode( $content );
		
		ob_start();
		?>
		<div class="youtube-embed">
			<div class="youtube-wrapper">
				<div class="iframe-wrapper" style="padding-bottom: <?php echo $percentage; ?>%;">
					<div id="<?php echo self::$player_id; ?>" class="yc_player" data-vid="<?php echo $atts['id']; ?>" data-play="<?php echo $atts['autoplay']; ?>" data-hide="<?php echo $atts['autohide']; ?>" data-theme="<?php echo $atts['theme']; ?>">
						<div class="error">
							<img src="<?php echo plugins_url( 'img/flashplayer.jpg', __FILE__ ); ?>" width=64 height=64 />
							<img src="<?php echo plugins_url( 'img/javascript.jpg', __FILE__ ); ?>" width=64 height=64 />
							<div>You need Flash player 8+ and JavaScript enabled to view this video.</div>
						</div>
					</div>
				</div>
			</div>
			<div class="youtube-controls">
				<ul>
					<?php if ( self::$title_counter == 0 ): ?>
						<li class="title">
							<div>
								<?php echo $atts['title']; ?>
							</div>
						</li>
					<?php endif; ?>
					<?php echo $content; ?>
				</ul>
			</div>
		</div>
		<?php
		self::$player_id = null;
		return ob_get_clean();
	}
	
	/**
	 * control_shortcode function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static function control_shortcode( $atts, $content ) {
		if ( self::$player_id != null ):
			$timestamp = $atts[0];
			$title = $atts[1];
			
			$seconds = 0;
			$segments = array_reverse( split( ':', $timestamp ) );
			$increments = array( 1, MINUTE_IN_SECONDS, HOUR_IN_SECONDS );
			
			for ( $i = 0; $i < count($segments); $i++ ):
				$seconds += $segments[$i] * $increments[$i];
			endfor;
			
			$action = "YouTube_Shortcode.skipTo('".self::$player_id."', ".$seconds.");";
			
			ob_start();
			?>
			<li class="control" onclick="<?php echo $action; ?>">
				<div class="control-inner">
					<?php echo $timestamp; ?> - <?php echo $title; ?>
				</div>
			</li>
			<?php
			return ob_get_clean();
		else:
			return $content;
		endif;
	}
	
	/**
	 * title_shortcode function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $atts
	 * @param mixed $content
	 * @return void
	 */
	public static function title_shortcode( $atts, $content ) {
		if ( self::$player_id != null ):
			self::$title_counter++;
			ob_start();
			?>
			<li class="title">
				<div class="control-inner">
					<?php echo $atts[0]; ?>
				</div>
			</li>
			<?php
			return ob_get_clean();
		else:
			return $content;
		endif;
	}
}

YouTube_Control_Shortcode::init();