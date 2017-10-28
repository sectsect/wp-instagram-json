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
 * Detect the enable S3 Upload.
 *
 * @return boolean "description".
 */
function wp_instagram_json_is_s3() {
	$s3_enable = get_option( 'wp_instagram_json_s3_enable' );
	$s3_key = get_option( 'wp_instagram_json_aws_credentials_key' );
	$s3_secret = get_option( 'wp_instagram_json_aws_credentials_secret' );
	$s3_region = get_option( 'wp_instagram_json_s3_region' );
	$s3_bucket = get_option( 'wp_instagram_json_s3_bucket' );
	$s3_path = get_option( 'wp_instagram_json_s3_path' );
	$bool = ( $s3_enable && $s3_key && $s3_secret && $s3_region && $s3_bucket && $s3_path ) ? true : false;

	return $bool;
}

/**
 * Set script tag for json file url.
 *
 * @return void "description".
 */
function wp_instagram_json_var() {
	if ( wp_instagram_json_is_s3() ) {
		if ( get_option( 'wp_instagram_json_s3_custom_url' ) ) {
			$customurl = rtrim( esc_url( get_option( 'wp_instagram_json_s3_custom_url' ) ), '/' );
			$url = $customurl . '/' . get_option( 'wp_instagram_json_s3_path' ) . '/instagram.json';
		} else {
			$url = 'https://' . get_option( 'wp_instagram_json_s3_bucket' ) . '.s3.amazonaws.com/' . get_option( 'wp_instagram_json_s3_path' ) . '/instagram.json';
		}
	} else {
		$url = plugin_dir_url( dirname( __FILE__ ) ) . 'json/instagram.json';
	}
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
