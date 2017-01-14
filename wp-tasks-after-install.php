<?php
/**
 * Plugin Name: WP Tasks After Install
 * Plugin URI: https://www.oscarabadfolgueira.com/plugins/wp-tasks-after-install
 * Description: Performs a number of necessary tasks after installing WordPress.
 * Author: Oscar Abad Folgueira
 * Author URI: http://www.oscarabadfolgueira.com/
 * Version: 1.0
 * License: GPLv2 or later
 * Text Domain: oaf-wptai
 * Domain Path: /languages/
 */

// Go away!!
if ( ! defined( 'WPINC' ) ) {
     die;
}

add_action( 'admin_init', 'oaf_wptai_remove_default_post');
add_action( 'admin_init', 'oaf_wptai_remove_default_page');
add_action( 'admin_init', 'oaf_wptai_change_uncategorized');
add_action( 'admin_init', 'oaf_wptai_set_permalink_postname' );
add_action( 'admin_init', 'oaf_wptai_delete_hello_plugin' );
add_action( 'admin_init', 'oaf_wptai_disable_comments_and_pings' );
add_action( 'admin_init', 'oaf_wptai_delete_config_sample_file' );
add_action( 'admin_init', 'oaf_wptai_delete_themes' );
oaf_wptai_activate_plugin ( 'akismet/akismet.php' );
add_action( 'admin_init', 'oaf_wptai_deactivate_this_plugin' );


// Remove default post 'Hello Word'
function oaf_wptai_remove_default_post() {
	
	if ( FALSE === get_post_status( 1 ) ) {
	   	// The post does not exist - do nothing.		
	} else {
	   	wp_delete_post(1);
	}
	
} // end of oaf_wptai_remove_default_post() function.

// Remove the default example page
function oaf_wptai_remove_default_page() {
	
	if ( FALSE === get_post_status( 2 ) ) {
	   	// The page does not exist - do nothing.		
	} else {
	   	wp_delete_post(2);
	}
	
} // end of oaf_wptai_remove_default_page() function


// Change the name and slug of default category to news
function oaf_wptai_change_uncategorized() {
	
	$term = term_exists('Uncategorized', 'category'); // check if 'uncategorized' category exists
	
	if ($term !== 0 && $term !== null) {  // if exists change name and slug
	  wp_update_term(1, 'category', array(
	  	'name' => 'News',
	  	'slug' => 'news'
	  ));
	}
	
} // end of oaf_wptai_change_uncategorized() function.


// Set permlinks to postname  /%postname%/
function oaf_wptai_set_permalink_postname() {
	
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%/' );
    
} // end of oaf_wptai_set_permalink_postname() function.


// remove Hello Dolly plugin
function oaf_wptai_delete_hello_plugin() {
	
        $plugins = array( 'hello.php' );
	delete_plugins( $plugins );
	
} // end of oaf_wptai_delete_hello_plugin function.


// Disable comments and trackbacks
function oaf_wptai_disable_comments_and_pings() {

	// Disable pings
	if( '' != get_option( 'default_ping_status' ) ) {
		update_option( 'default_ping_status', '' );
	} // end if

	// Disable comments
	if( '' != get_option( 'default_comment_status' ) ) {
		update_option( 'default_comment_status', '' );
	} // end if

} // end oaf_wptai_disable_comments_and_pings() function.


// Delete wp-config-sample.php file
function oaf_wptai_delete_config_sample_file() {
	
	$url_config_sample = "wp-config-sample.php";
	$abspath=$_SERVER['DOCUMENT_ROOT'];
	$file_url = $abspath . '/' . $url_config_sample;
	if (file_exists($file_url)) {
	    unlink($file_url);
	}

} // end of oaf_wptai_delete_config_sample_file() function.


// Remove unactivated themes
function oaf_wptai_delete_themes() {

	// The current themes.
	$installed_themes = wp_get_themes();

	// The themes we want to keep (delete the others).
	$theme_data = wp_get_theme();
	$current_theme = $theme_data->get( 'TextDomain' );

	$themes_to_keep = array( $current_theme );

	// Loop through installed themes.
	foreach ( $installed_themes as $theme ) {

		// The name of the theme.
		$name = $theme->get_template();

		// If it's not one we want to keep...
		if ( ! in_array( $name, $themes_to_keep ) ) {
			$stylesheet = $theme->get_stylesheet();

			// Delete the theme.
			delete_theme( $stylesheet, false );
		}
	} // end of foreach - themes
	
} // end of oaf_wptai_delete_themes() function.


// Activate plugin
function oaf_wptai_activate_plugin( $plugin ) {
    $current = get_option( 'active_plugins' );
    $plugin = plugin_basename( trim( $plugin ) );

    if ( !in_array( $plugin, $current ) ) {
        $current[] = $plugin;
        sort( $current );
        do_action( 'activate_plugin', trim( $plugin ) );
        update_option( 'active_plugins', $current );
        do_action( 'activate_' . trim( $plugin ) );
        do_action( 'activated_plugin', trim( $plugin) );
    }

    return null;
    
} // end of oaf_wptai_activate_plugin() function.


// Deactivate this plugin.
function oaf_wptai_deactivate_this_plugin() {

	if ( !function_exists( 'deactivate_plugins' ) ) { 
	    require_once ABSPATH . '/wp-admin/includes/plugin.php'; 
	} 
	
	deactivate_plugins( plugin_basename( __FILE__ ) );
	
} // end of oaf_wptai_deactivate_this_plugin() function.
