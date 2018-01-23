<?php
/**
 * Plugin Name:     WP Instagram JSON
 * Plugin URI:      https://github.com/sectsect/wp-instagram-json
 * Description:     Generate JSON file with object data returned from Instagram API for Sandbox Mode
 * Author:          SECT INTERACTIVE AGENCY
 * Author URI:      https://www.ilovesect.com/
 * Text Domain:     wp-instagram-json
 * Domain Path:     /languages
 * Version:         1.1.5
 *
 * @package         WP_Instagram_JSON
 */

$wp_instagram_json_minimalrequiredphpversion = '5.5';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version.
 *
 * @return void "description".
 */
function wp_instagram_json_noticephpversionwrong() {
	global $wp_instagram_json_minimalrequiredphpversion;
	echo '<div class="updated fade">' .
	__( 'Error: plugin "WP Instagram JSON" requires a newer version of PHP to be running.', 'wp_instagram_json' ) .
			'<br/>' . __( 'Minimal version of PHP required: ', 'wp_instagram_json' ) . '<strong>' . $wp_instagram_json_minimalrequiredphpversion . '</strong>' .
			'<br/>' . __( 'Your server\'s PHP version: ', 'wp_instagram_json' ) . '<strong>' . phpversion() . '</strong>' .
		'</div>';
}

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version.
 *
 * @return boolean "description".
 */
function wp_instagram_json_phpversioncheck() {
	global $wp_instagram_json_minimalrequiredphpversion;
	if ( version_compare( phpversion(), $wp_instagram_json_minimalrequiredphpversion ) < 0 ) {
		add_action( 'admin_notices', 'wp_instagram_json_noticephpversionwrong' );
		return false;
	}
	return true;
}

/**
 * The code that runs during plugin activation.
 *
 * @return void "description".
 */
function activate_wp_instagram_json() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-instagram-json-activator.php';
	WP_Instagram_JSON_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-instagram-json-deactivator.php
 */
function deactivate_wp_instagram_json() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-instagram-json-deactivator.php';
	WP_Instagram_JSON_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_instagram_json' );
register_deactivation_hook( __FILE__, 'deactivate_wp_instagram_json' );

/**
 * Load the textdomain.
 *
 * @return void "description".
 */
function wp_instagram_json_load_textdomain() {
	load_plugin_textdomain( 'wp-instagram-json', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wp_instagram_json_load_textdomain' );

/**
 * Add my meta data to row.
 *
 * @param  array  $plugin_meta "description".
 * @param  string $plugin_file "description".
 * @param  string $plugin_data "description".
 * @param  string $status      "description".
 * @return statement           "description".
 */
function wp_instagram_json_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( plugin_basename( __FILE__ ) == $plugin_file ) {
		$plugin_meta[] = '<a href="https://github.com/sectsect/wp-instagram-json" target="_blank"><span class="dashicons dashicons-randomize"></span> GitHub</a>';
		return $plugin_meta;
	}
}
add_filter( 'plugin_row_meta', 'wp_instagram_json_row_meta', 10, 4 );

if ( wp_instagram_json_phpversioncheck() ) {
	require_once plugin_dir_path( __FILE__ ) . 'functions/functions.php';
	if ( wp_instagram_json_is_s3() && file_exists( plugin_dir_path( __FILE__ ) . 'composer/vendor/autoload.php' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'composer/vendor/autoload.php';
	}
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-instagram-json.php';
	new WP_Instagram_JSON();
	require_once plugin_dir_path( __FILE__ ) . 'includes/admin.php';
}
