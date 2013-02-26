<?php
/*
Plugin Name: wp-backstretch
Plugin URI: https://github.com/benjick/wp-backstretch
Description: Adds the jQuery backstretch plugin to WP
Version: 0.1
Author: benjick
Author URI: http://maxmalm.se
License: GPL2

jQuery Backstretch is made by Scott Robbin
http://srobbin.com/blog/jquery-plugins/jquery-backstretch/

Download the jQuery plugin from https://github.com/srobbin/jquery-backstretch/zipball/master
and drop it in the wp-backstretch folder
*/

/*******************************
 GUI 
 *******************************/

add_action('admin_menu', 'wp_backstretch_menu');
function wp_backstretch_menu() {
	add_submenu_page( 'themes.php', 'jQuery Backstrech', 'Backstrech', 'manage_options', 'wp-backstretch', 'wp_backstretch' ); 
}

function wp_backstretch() {
$options = get_option('backstretch_options');
$url = $options['backstretch_url'];
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div><h2><?php _e('jQuery Backstretch','wp-backstretch'); ?></h2>

<form action="options.php" method="post">
<?php settings_fields('backstretch_options'); ?>
<?php do_settings_sections('plugin'); ?>

<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>

<?php if($url!="") { ?>
	<h3><?php _e('Current image','wp-backstretch'); ?></h3>
	<img src="<?php echo $url; ?>" style="max-width:50%;" />
<?php } ?>

</div>
<?php
}

/*******************************
 Form
 *******************************/

add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){
	register_setting( 'backstretch_options', 'backstretch_options', 'backstretch_options_validate' );
	add_settings_section('plugin_main', __('Settings','wp-backstretch'), 'backstretch_section_text', 'plugin');
	add_settings_field('backstretch_url', __('URL to background','wp-backstretch'), 'backstretch_setting_string', 'plugin', 'plugin_main');
}

function backstretch_setting_string() {
	$options = get_option('backstretch_options');
	#var_dump($options);
	echo "<input id='plugin_text_string' name='backstretch_options[backstretch_url]' style='width:100%;' type='text'' value='{$options['backstretch_url']}' />";
	#var_dump($_POST);
}

function backstretch_section_text() {
	_e('Pick an background image to stretch','wp-backstretch');
}

function backstretch_options_validate($input) {
	// no validation... yet
	return $input;
}

/*******************************
 Print
 *******************************/

function register_backstretch() {
    #wp_register_script( 'backstretch', plugin_dir_url( __FILE__ ) . 'jquery.backstretch.min.js');
    #wp_enqueue_script( 'backstretch' ); // old method with drop in place - keeping it if someone wants to use it
    wp_enqueue_script( 'backstretch', 'http://cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.3/jquery.backstretch.min.js');
}    
 
add_action('wp_enqueue_scripts', 'register_backstretch');

// Shortcode 
function backstretch_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'url' => '',
	), $atts ) );
	
	if($url=='') {
		return false;
	}
	$var = '<script type="text/javascript">jQuery(document).ready(function($){
    $.backstretch("' . $url . '");
});</script>';
	return $var;
}
add_shortcode( 'backstretch', 'backstretch_shortcode' );

function backstretch_head() {
	$options = get_option('backstretch_options');
	$url = $options['backstretch_url'];
	if($url=="") {
		return false;
	}
	$var = '<script type="text/javascript">jQuery(document).ready(function($){
    $.backstretch("' . $url . '");
});</script>';
	echo $var;
}
add_action('wp_head', 'backstretch_head');
