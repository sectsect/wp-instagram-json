<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link       https://github.com/sectsect/
 * @since      1.0.0
 *
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/functions
 */

/**
 * Register functions for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/functions
 */

/**
 * Set script tag for json file url.
 *
 * @return void "description".
 */
function wp_instagram_json_var() {
	$url = plugin_dir_url( dirname( __FILE__ ) ) . 'json/instagram.json';
	$cache = get_option( '_transient_wp_instagram_json' );
	$datetime = ( $cache ) ? $cache : false;
	$array = array(
		'json_url'      => $url,
		'generate_time' => $datetime,
	);
	$obj = json_encode( $array );
	echo '<script>var wp_ig_json = ' . $obj . ';</script>';
}
add_action( 'wp_head', 'wp_instagram_json_var' );
