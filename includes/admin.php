<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/sectsect/
 * @since      1.0.0
 *
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/includes
 */

add_action('admin_menu', 'wp_instagram_json_menu');
/**
 * Add options page.
 *
 * @return void "description".
 */
function wp_instagram_json_menu() {
	$page_hook_suffix = add_menu_page('Instagram', 'Instagram', 5, 'instagram_menu', 'wp_instagram_json_options_page');
	add_action('admin_print_styles-' . $page_hook_suffix, 'wp_instagram_json_admin_styles');
    add_action('admin_print_scripts-' . $page_hook_suffix, 'wp_instagram_json_admin_scripts');
	add_action('admin_init', 'register_wp_instagram_json_settings');
}

/**
 * Style setting.
 *
 * @return void "description".
 */
function wp_instagram_json_admin_styles() {
	wp_enqueue_style('bootstrap-switch', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css', array());
}

/**
 * Script setting.
 *
 * @return void "description".
 */
function wp_instagram_json_admin_scripts() {
    wp_enqueue_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'));
	wp_enqueue_script('bootstrap-switch', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js', array('bootstrap'));
    wp_enqueue_script('script', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/assets/js/script.js', array('bootstrap-switch'));
}

/**
 * Register setting.
 *
 * @return void "description".
 */
function register_wp_instagram_json_settings() {
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_cache_time');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_count');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_account_name');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_access_token');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_s3_enable');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_aws_credentials_key');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_aws_credentials_secret');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_s3_region');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_s3_bucket');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_s3_path');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_s3_custom_url');
}

/**
 * Require file.
 *
 * @return void "description".
 */
function wp_instagram_json_options_page() {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/index.php';
}
