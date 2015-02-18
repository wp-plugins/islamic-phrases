<?php
/*
Plugin Name: Islamic Phrases
Plugin URI: http://www.kloningspoon.com/islamic-phrases/
Description: Add Islamic phrases like Azza wa Jalla, Radhiyallahu 'anhu etc. Setup your default font size at <strong>General Settings</strong> or you can add manually within the shortcode and you can change the pronounce too, <strong>[Islamic phrases="Allah" font_size="30"]x[/Islamic]</strong>.
Author: Darto KLoning
Version: 2.12.2015
Author URI: http://www.kloningspoon.com/

Copyright 2014  Darto KLoning (email: darto@kloningspoon.com)

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


$new_general_setting = new new_general_setting();
class new_general_setting {
    function new_general_setting( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'Islamic_Phrases_Font_Size', 'esc_attr' );
        add_settings_field('Islamic_Phrases_Font_Size', '<label for="Islamic_Phrases_Font_Size">'.__('Islamic Phrases Font Size' , 'Islamic_Phrases_Font_Size' ).'</label>' , array(&$this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'Islamic_Phrases_Font_Size', '' );
        echo '<input class="small-text" type="text" id="Islamic_Phrases_Font_Size" name="Islamic_Phrases_Font_Size" value="' . $value . '" />&nbsp;px&nbsp;&nbsp;<span id="IsP_errmsg"></span>';
    }
}

add_action('admin_head', 'IsP_admin_script');
function IsP_admin_script() {
echo '<script type="text/javascript">jQuery(document).ready(function(e){e("#Islamic_Phrases_Font_Size").keypress(function(s){return 8!=s.which&&0!=s.which&&(s.which<48||s.which>57)?(e("#IsP_errmsg").html("Numbers only please...").css("color","red").show().fadeOut("slow"),!1):void 0})});</script>';
}

/*---------------------------------------------------
Hooks TinyMCE buttons
----------------------------------------------------*/
function islamic_phrases_mce_button() {
	// check user permissions
	if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
		return;
	}
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'add_islamic_phrases_tinymce_plugin' );
		add_filter( 'mce_buttons', 'register_islamic_phrases_mce_button' );
	}
}
add_action('admin_head', 'islamic_phrases_mce_button');

// Declare script for new button
function add_islamic_phrases_tinymce_plugin( $plugin_array ) {
	$plugin_array['islamic_phrases_button'] = plugins_url('/js/mce-button.js', __FILE__);
	return $plugin_array;
}

// Register new button in the editor
function register_islamic_phrases_mce_button( $buttons ) {
	array_push( $buttons, 'islamic_phrases_button' );
	return $buttons;
}


add_action('wp_enqueue_scripts','IsP_style');
function IsP_style() {
wp_register_style('islamic-phrases-css', plugins_url('/css/islamic-phrases.css', __FILE__),'',null);
wp_enqueue_style('islamic-phrases-css');
}

function islamic_phrases_function($atts, $content = null) {
    extract(shortcode_atts(array(
		"font_size" => "",
		"phrases" => ""
    ), $atts));
	
	if (!$font_size) {$font_size = get_option('Islamic_Phrases_Font_Size');}
	$islamic_phrases = '<span data-tooltip="'.$phrases.'" style="font-family: Islamic Phrases; font-size:'.$font_size.'px; direction: rtl;">'.$content.'</span>';
	
    return $islamic_phrases;
}

add_shortcode("Islamic", "islamic_phrases_function");