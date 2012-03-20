<?php
/*
Plugin Name: Accordion Shortcode
Plugin URI: http://wordpress.org/extend/plugins/accordion-shortcode/
Description: Adds shortcode that enables you to create accordions 
Author: CTLT 
Version: 1.1
Author URI: http://ctlt.ubc.ca
*/
global $olt_accordion_shortcode_count;
$olt_accordion_shortcode_count = 0;

function olt_display_shortcode_accordion($atts,$content)
{
	global $olt_accordion_shortcode_count,$post;
	extract(shortcode_atts(array(
		'title' => null,
		'class' => null,
	), $atts));
	
	ob_start();
	
	if($title):
		?>
		
		<h3 ><a href="#<?php echo ereg_replace("[^A-Za-z0-9]", "", $title)."-".$olt_accordion_shortcode_count; ?>"><?php echo $title; ?></a></h3>
		<div class="accordian-shortcode-content <?php echo $class; ?>" >
			<?php echo do_shortcode( $content ); ?>
		</div>
		<?php
	elseif($post->post_title):
	?>
		<div id="<?php echo ereg_replace("[^A-Za-z0-9]", "", $post->post_title)."-".$olt_accordion_shortcode_count; ?>" >
			<?php echo do_shortcode( $content ); ?>
		</div>
	<?php
	else:
	?>
		<span style="color:red">Please enter a title attribute like [accordion title="title name"]accordion content[accordion]</span>
		<?php 	
	endif;
	$olt_accordion_shortcode_count++;
	return ob_get_clean();
}

function olt_display_shortcode_accordions($attr,$content)
{
	// wordpress function 
	global $olt_accordion_shortcode_count,$post;
	
	$attr['autoHeight'] =  (bool)$attr['autoHeight'];
	$attr['disabled'] =  (bool)$attr['disabled'];
	$attr['active'] =  (int)$attr['active'];
	$attr['clearStyle'] = (bool)$attr['clearStyle'];
	$attr['collapsible'] = (bool)$attr['collapsible'];
	$attr['fillSpace']= (bool)$attr['fillSpace'];
	$query_atts = shortcode_atts(
		array( 
			'autoHeight' => false, 
			'disabled' => false,
			'active'	=> 0,
			'animated' => 'slide',
			'clearStyle' => false,
			'collapsible' => false,
			'event'=>'click',
			'fillSpace'=>false
		), $attr);
	
	// there might be a better way of doing this
	$id = "random-accordion-id-".rand(0,1000);
	
	$content = (substr($content,0,6) =="<br />" ? substr($content,6): $content);
	$content = str_replace("]<br />","]",$content);
	ob_start();
	?>
	<div id="<?php echo $id ?>" class="accordions-shortcode">
		<?php echo do_shortcode( $content ); ?> 
	</div>
	<script type="text/javascript"> /* <![CDATA[ */ 
	jQuery(document).ready( function($){ $("#<?php echo $id ?>").accordion(<?php echo json_encode($query_atts); ?> ); }); 
	/* ]]> */ </script>

	<?php
	$post_content = ob_get_clean();
	
	 wp_enqueue_script('jquery');
     wp_enqueue_script('jquery-ui-core');
     wp_enqueue_script('jquery-ui-accordion');
	return str_replace("\r\n", '',$post_content);
}

function olt_accordions_shortcode_init() {
    
    add_shortcode('accordion', 'olt_display_shortcode_accordion'); // Individual accordion
    add_shortcode('accordions', 'olt_display_shortcode_accordions'); // The shell
	
}

add_action('init','olt_accordions_shortcode_init');