<?php
/*
Plugin Name: wp-backstretch
Plugin URI: https://github.com/benjick/wp-backstretch
Description: Adds the jQuery backstretch plugin to WP
Version: 1.0
Author: benjick
Author URI: http://maxmalm.se
License: GPL2

jQuery Backstretch is made by Scott Robbin
http://srobbin.com/blog/jquery-plugins/jquery-backstretch/

Download the jQuery plugin from https://github.com/srobbin/jquery-backstretch/zipball/master
and drop it in the wp-backstretch folder
*/

/*  Copyright 2013  Max Malm  (email : benjick@dumfan.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*******************************
 GUI 
 *******************************/

add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );

add_action('admin_menu', 'wp_backstretch_menu');
function wp_backstretch_menu() {
	add_submenu_page( 'themes.php', 'jQuery Backstretch', 'Backstretch', 'switch_themes', 'wp-backstretch', 'wp_backstretch' ); 
}

function wp_backstretch() {
$options = get_option('backstretch_options');
$url = $options['backstretch_url'];
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div><h2><?php _e('jQuery Backstretch','wp-backstretch'); ?></h2>
<?php if($_GET['settings-updated'] == "true") { ?>
<div id="message" class="updated"><p><?php _e('Settings saved.','wp-backstretch'); ?></p></div>
<?php } ?>

<form action="options.php" method="post">
<?php settings_fields('backstretch_options'); ?>
<?php do_settings_sections('plugin'); ?>

<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
<!-- Use the fancy uploader -->
<script type="text/javascript">
jQuery(document).ready(function(e){var t=true,n=wp.media.editor.send.attachment;e("#backstretch_media").click(function(r){var i=wp.media.editor.send.attachment;var s=e(this);var o="backstretch_url";t=true;wp.media.editor.send.attachment=function(r,i){if(t){e("#"+o).val(i.url)}else{return n.apply(this,[r,i])}};wp.media.editor.open(s);return false});e(".add_media").on("click",function(){t=false})})
</script>

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
	?>
	<input id='backstretch_url' name='backstretch_options[backstretch_url]' type='text' value='<?php echo $options['backstretch_url']; ?>' />
	<input class="button" name="uploadbutton" id="backstretch_media" value="Upload" />
	<?php
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
